<?php

declare(strict_types=1);

namespace Mediaca\Crossposting\Vk\Api;

use Mediaca\Crossposting\ChannelConfig\VkChannelConfig;
use Mediaca\Crossposting\Iblock\ElementGateway;
use Mediaca\Crossposting\Sender;
use Mediaca\Crossposting\Server;
use Mediaca\Crossposting\Template\TemplateParser;
use Mediaca\Crossposting\Vk\Api\WallPost\Attachments;
use Mediaca\Crossposting\Vk\Api\WallPost\LinkAttachment;
use Mediaca\Crossposting\Vk\Api\WallPost\MediaAttachment;
use Mediaca\Crossposting\Vk\Api\WallPost\TypeMediaAttachment;

class VkontakteSender implements Sender
{
    private readonly TemplateParser $parser;

    public function __construct(
        private readonly ElementGateway $elementGateway,
        private readonly VkontakteApiClient $client,
        private readonly Server $server,
        private readonly VkChannelConfig $config,
    ) {
        $this->parser = new TemplateParser($this->config->messageTemplate);
    }

    public function send(int $elementId): void
    {
        $data = $this->getData($elementId);
        $photos = self::getFiles($data, $this->config->dataPhotos);

        $message = $this->parser->build($data);
        $attachments = new Attachments();
        $attachments->addAttachment(new LinkAttachment(
            (str_starts_with('/', $data['DETAIL_PAGE_URL']) ? $this->server->getDomain() : '') . $data['DETAIL_PAGE_URL'],
        ));

        if (!$this->config->useAllPhotos && count($photos) > 1) {
            $photos = array_slice($photos, 0, 1);
        }

        foreach ($photos as $photo) {
            $photo = $this->client->uploadWallPhotoAndSave(
                $this->config['ownerId'],
                $this->server->getDocumentRoot() . $photo['src'],
            );

            $attachments->addAttachment(
                new MediaAttachment(TypeMediaAttachment::PHOTO, $photo['owner_id'], $photo['id']),
            );
        }

        $this->client->wallPost(
            $this->config['ownerId'],
            $this->config['fromGroup'] ?? false,
            $message,
            $attachments,
        );
    }

    private function getData(int $elementId): array
    {
        $dataCodes = array_merge($this->parser->getDataCodes(), $this->config->dataPhotos);
        $filter = ['ID' => $elementId, 'ACTIVE' => 'Y'];

        return $this->elementGateway->require($dataCodes, $filter);
    }

    private static function getFiles(array $element, array $dataCodes): array
    {
        $dataCodes = array_filter($dataCodes, static fn($code) => $element[$code]);
        if (!$dataCodes) {
            return [];
        }

        $ids = [];
        foreach ($dataCodes as $code) {
            $value = $element[$code];
            if (is_array($value)) {
                $ids = array_merge($ids, $value);
            } else {
                $ids[] = $value;
            }
        }

        $rawFiles = [];
        $db = \CFile::GetList([], ['@id' => implode(',', $ids)]);
        while ($rawFile = $db->Fetch()) {
            $rawFile['SRC'] = \CFile::GetFileSRC($rawFile);

            $rawFiles[$rawFile['ID']] = $rawFile;
        }

        $result = [];
        foreach ($dataCodes as $code) {
            $value = $element[$code];

            if (!is_array($value)) {
                $result[] = $rawFiles[$value];

                continue;
            }

            foreach ($value as $valueItem) {
                $result[] = $rawFiles[$valueItem];
            }
        }

        return $result;
    }
}
