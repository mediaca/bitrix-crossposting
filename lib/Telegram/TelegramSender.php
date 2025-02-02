<?php

declare(strict_types=1);

namespace Mediaca\Crossposting\Telegram;

use Mediaca\Crossposting\ChannelConfig\TelegramChannelConfig;
use Mediaca\Crossposting\Iblock\ElementGateway;
use Mediaca\Crossposting\Sender;
use Mediaca\Crossposting\Server;
use Mediaca\Crossposting\Template\TemplateParser;

class TelegramSender implements Sender
{
    private readonly TemplateParser $parser;

    public function __construct(
        private readonly ElementGateway $elementGateway,
        private readonly TelegramBotClient $client,
        private readonly Server $server,
        private readonly TelegramChannelConfig $config,
    ) {
        $this->parser = new TemplateParser($this->config->messageTemplate);
    }

    public function send(int $elementId): void
    {
        $data = $this->getData($elementId);
        $photo = self::getFile($data, $this->config->dataPhotos);

        $message = $this->parser->build($data);
        $url = (str_starts_with('/', $data['DETAIL_PAGE_URL']) ? $this->server->getDomain() : '') . $data['DETAIL_PAGE_URL'];

        $text = new TextTelegramMessage($message, "\n\n$url");

        $photo ? $this->client->sendPhoto($this->server->getDocumentRoot() . $photo['src'], $text)
            : $this->client->sendMessage($text);
    }

    private function getData(int $elementId): array
    {
        $dataCodes = array_merge($this->parser->getDataCodes(), $this->config->dataPhotos);
        $filter = ['ID' => $elementId, 'ACTIVE' => 'Y'];

        return $this->elementGateway->require($dataCodes, $filter);
    }

    private static function getFile(array $element, array $dataCodes): ?array
    {
        $fileId = null;
        foreach ($dataCodes as $code) {
            $value = $element[$code];
            if ($value) {
                $fileId = $value;
                break;
            }
        }

        if (!$fileId) {
            return [];
        }

        $db = \CFile::GetList([], ['id' => $fileId]);
        if ($rawFile = $db->Fetch()) {
            $rawFile['src'] = \CFile::GetFileSRC($rawFile);

            return $rawFile;
        }

        return null;
    }
}
