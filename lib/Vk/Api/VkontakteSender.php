<?php

declare(strict_types=1);

namespace Mediaca\Crossposting\Vk\Api;

use Mediaca\Crossposting\Server;
use Mediaca\Crossposting\Template\TemplateParser;
use Mediaca\Crossposting\Vk\Api\WallPost\Attachments;
use Mediaca\Crossposting\Vk\Api\WallPost\LinkAttachment;
use Mediaca\Crossposting\Vk\Api\WallPost\MediaAttachment;
use Mediaca\Crossposting\Vk\Api\WallPost\TypeMediaAttachment;

class VkontakteSender
{
    public function __construct(
        private readonly VkontakteApiClient $client,
        private readonly TemplateParser $parser,
        private readonly Server $server,
        private readonly array $config,
    ) {}

    public function send(array $data, ?array $photo): void
    {
        $message = $this->parser->build($data);

        $attachments = new Attachments();
        $attachments->addAttachment(new LinkAttachment(
            (str_starts_with('/', $data['DETAIL_PAGE_URL']) ? $this->server->getDomain() : '') . $data['DETAIL_PAGE_URL'],
        ));

        if ($photo) {
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
}
