<?php

declare(strict_types=1);

namespace Mediaca\Crossposting;

use Bitrix\Main\Config\Configuration;
use Bitrix\Main\Context;
use Bitrix\Main\Type\DateTime;
use Mediaca\Crossposting\Task\Channel;
use Mediaca\Crossposting\Task\TaskGateway;

class TaskAdder
{
    public static function addByEvent(array $fields): void
    {
        if (!$fields['RESULT']) {
            return;
        }

        $iblocks = Configuration::getValue(Module::ID)['main']['iblocks'] ?? [];
        if (!in_array((int) $fields['IBLOCK_ID'], $iblocks, true)) {
            return;
        }

        $request = Context::getCurrent()->getRequest();

        foreach (Channel::cases() as $channel) {
            $fieldName = CrosspostingTab::getFieldNameChannel($channel);

            if (!empty($request->getPost($fieldName))) {
                TaskGateway::add(new DateTime(), (int) $fields['ID'], $channel);
            }
        }
    }
}
