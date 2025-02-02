<?php

declare(strict_types=1);

namespace Mediaca\Crossposting;

use Bitrix\Main\Web\HttpClient;
use Mediaca\Crossposting\Config\ChannelConfigFactory;
use Mediaca\Crossposting\Config\TelegramChannelConfig;
use Mediaca\Crossposting\Config\VkChannelConfig;
use Mediaca\Crossposting\Iblock\ElementGateway;
use Mediaca\Crossposting\Task\Channel;
use Mediaca\Crossposting\Telegram\TelegramBotClient;
use Mediaca\Crossposting\Telegram\TelegramSender;
use Mediaca\Crossposting\Vk\Api\VkontakteApiClient;
use Mediaca\Crossposting\Vk\Api\VkontakteSender;

class SenderFactory
{
    public static function build(
        Channel $channel,
        array $config,
    ): Sender {
        $channelConfig = ChannelConfigFactory::build($channel, $config);
        $elementGateway = new ElementGateway();

        $httpClient = new HttpClient();
        $client = match (true) {
            ($channelConfig instanceof VkChannelConfig) => new VkontakteApiClient($httpClient, $channelConfig->accessToken),
            ($channelConfig instanceof TelegramChannelConfig) => new TelegramBotClient($httpClient, $channelConfig->accessToken, $channelConfig->chatUserName),
        };
        $server = new Server();

        if ($client instanceof VkontakteApiClient) {
            return new VkontakteSender($elementGateway, $client, $server, $channelConfig);
        }

        return new TelegramSender($elementGateway, $client, $server, $channelConfig);
    }
}
