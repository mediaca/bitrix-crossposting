<?php

declare(strict_types=1);

namespace Mediaca\Crossposting\Config;

use Mediaca\Crossposting\Task\Channel;

class ChannelConfigFactory
{
    public static function build(Channel $channel, array $config): ChannelConfig
    {
        return match ($channel) {
            Channel::VK => new VkChannelConfig($config[$channel->value]),
            Channel::TELEGRAM => new TelegramChannelConfig($config[$channel->value]),
        };
    }
}
