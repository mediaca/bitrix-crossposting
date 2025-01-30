<?php

declare(strict_types=1);

namespace Mediaca\Crossposting\Telegram;

use Mediaca\Crossposting\Sender;
use Mediaca\Crossposting\Server;
use Mediaca\Crossposting\Template\TemplateParser;

class TelegramSender implements Sender
{
    public function __construct(
        private readonly TelegramBotClient $client,
        private readonly TemplateParser $parser,
        private readonly Server $server,
    ) {}

    public function send(array $data, array $photos): void
    {
        $message = $this->parser->build($data);
        $url = (str_starts_with('/', $data['DETAIL_PAGE_URL']) ? $this->server->getDomain() : '') . $data['DETAIL_PAGE_URL'];

        $text = new TextTelegramMessage($message, "\n\n$url");

        $photos ? $this->client->sendPhoto($this->server->getDocumentRoot() . $photos[0]['src'], $text)
            : $this->client->sendMessage($text);
    }
}
