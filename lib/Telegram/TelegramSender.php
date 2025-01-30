<?php

declare(strict_types=1);

namespace Mediaca\Crossposting\Telegram;

use Mediaca\Crossposting\Server;
use Mediaca\Crossposting\Template\TemplateParser;

class TelegramSender
{
    public function __construct(
        private readonly TelegramBotClient $client,
        private readonly TemplateParser $parser,
        private readonly Server $server,
    ) {}

    private function send(array $data, ?array $photo): void
    {
        $message = $this->parser->build($data);
        $url = (str_starts_with('/', $data['DETAIL_PAGE_URL']) ? $this->server->getDomain() : '') . $data['DETAIL_PAGE_URL'];

        $text = new TextTelegramMessage($message, "\n\n$url");

        $photo ? $this->client->sendPhoto($this->server->getDocumentRoot() . $photo['src'], $text)
            : $this->client->sendMessage($text);
    }
}
