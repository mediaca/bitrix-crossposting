<?php

declare(strict_types=1);

namespace Mediaca\Crossposting\Task;

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

enum Channel: string
{
    case VK = 'vk';
    case TELEGRAM = 'telegram';
}
