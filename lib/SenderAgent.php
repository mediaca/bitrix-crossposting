<?php

declare(strict_types=1);

namespace Mediaca\Crossposting;

use Bitrix\Main\Web\HttpClient;
use Mediaca\Crossposting\ChannelConfig\VkChannelConfig;
use Mediaca\Crossposting\Exception\Vk\UserAuthorizationFailException;
use Bitrix\Main\Config\Configuration;
use Mediaca\Crossposting\Task\Channel;
use Mediaca\Crossposting\Task\TaskGateway;
use Mediaca\Crossposting\Vk\Id\VkIdClient;

class SenderAgent
{
    public static function run(): string
    {
        // @завершение задач
        $tasks = TaskGateway::fetchUnExecTasks(20);
        $config = Configuration::getValue(Module::ID);

        foreach ($tasks as $task) {
            try {
                $sender = SenderFactory::build($task['channel'], $config);
                $sender->send($task['elementId']);
            } catch (UserAuthorizationFailException) {
                $config = self::updateVkTokens($config);

                $sender = SenderFactory::build($task['channel'], $config);
                $sender->send($task['elementId']);
            }
        }

        return '\\' . __METHOD__ . '();';
    }

    private static function updateVkTokens(array $config): array
    {
        $channelConfig = new VkChannelConfig($config[Channel::VK->value]);
        $client = new VkIdClient(new HttpClient(), $channelConfig->clientId);

        $response = $client->updateAccessToken($channelConfig->refreshToken, $channelConfig->deviceId, null);

        $channelConfig->accessToken = $response['access_token'];
        $channelConfig->refreshToken = $response['refresh_token'];

        $config[Channel::VK->value] = $channelConfig->getValues();

        Configuration::setValue(Module::ID, $config);

        return $config;
    }
}
