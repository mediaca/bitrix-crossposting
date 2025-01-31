<?php

declare(strict_types=1);

namespace Mediaca\Crossposting;

use Bitrix\Main\Web\HttpClient;
use Mediaca\Crossposting\Task\Channel;
use Mediaca\Crossposting\Telegram\TelegramBotClient;
use Mediaca\Crossposting\Telegram\TelegramSender;
use Mediaca\Crossposting\Template\TemplateParser;
use Mediaca\Crossposting\Vk\Api\VkontakteApiClient;
use Mediaca\Crossposting\Vk\Api\VkontakteSender;

class SenderFactory
{
    private readonly TemplateParser $parser;
    private readonly Sender $sender;
    private readonly array $dataPhotos;

    public function __construct(
        Channel $channel,
        array $config,
    ) {
        $httpClient = new HttpClient();

        $client = match ($channel) {
            Channel::VK => new VkontakteApiClient($httpClient, $config['vk']['accessToken']),
            Channel::TELEGRAM => new TelegramBotClient(
                $httpClient,
                $config['telegram']['accessToken'],
                $config['vk']['telegram'],
            ),
        };
        $server = new Server();


        if ($client instanceof VkontakteApiClient) {
            $this->parser = new TemplateParser($config['vk']['messageTemplate']);
            $this->sender = new VkontakteSender($client, $this->parser, $server, $config);
            $this->dataPhotos = $config['vk']['dataPhotos'] ?? [];
        } else {
            $this->parser = new TemplateParser($config['telegram']['messageTemplate']);
            $this->sender = new TelegramSender($client, $this->parser, $server);
            $this->dataPhotos = $config['telegram']['dataPhotos'] ?? [];
        }
    }

    public function getSender(): Sender
    {
        return $this->sender;
    }

    public function getTemplateParser(): TemplateParser
    {
        return $this->parser;
    }

    public function getDataPhotos(): array
    {
        return $this->dataPhotos;
    }
}
