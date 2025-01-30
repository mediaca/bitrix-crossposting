<?php

declare(strict_types=1);

namespace Mediaca\Crossposting\Telegram;

use Mediaca\Crossposting\Template\TemplateParser;

class TelegramSender
{
    public function __construct(
        private readonly TemplateParser $templateParser,
        private readonly TelegramBotClient $client,
        private readonly string $domain,
        private readonly string $documentRoot,
    ) {}

    private function send(array $data, ?array $photo): void
    {
        $message = $this->templateParser->build($data);
        $url = (str_starts_with('/', $data['DETAIL_PAGE_URL']) ? $this->domain : '') . $data['DETAIL_PAGE_URL'];

        $text = new TextTelegramMessage($message, "\n\n$url");

        $photo ? $this->client->sendPhoto($this->documentRoot . $photo['src'], $text)
            : $this->client->sendMessage($text);
    }
}
