<?php

declare(strict_types=1);

namespace Mediaca\Crossposting\Task;

enum Channel: string
{
    case VK = 'vkontakte';
    case TELEGRAM = 'telegram';
}
