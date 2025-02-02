<?php

declare(strict_types=1);

namespace Mediaca\Crossposting;

use Bitrix\Main\Config\Configuration;
use Bitrix\Main\Context;
use Bitrix\Main\Localization\Loc;
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

        $config = Configuration::getValue(Module::ID)['main'] ?? [];
        $iblocks = $config['iblocks'] ?? [];
        $notifyCreatedTasks = $config['notifyCreatedTasks'] ?? false;

        if (!in_array((int) $fields['IBLOCK_ID'], $iblocks, true)) {
            return;
        }

        $request = Context::getCurrent()->getRequest();
        $useChannels = [];

        foreach (Channel::cases() as $channel) {
            $fieldName = CrosspostingTab::getFieldNameChannel($channel);

            if (!empty($request->getPost($fieldName))) {
                $useChannels[] = Loc::getMessage('MEDIACA_CROSSPOSTING_CHANNEL_' . strtoupper($channel->value));
                TaskGateway::add(new DateTime(), (int) $fields['ID'], $channel);
            }
        }

        if ($notifyCreatedTasks && $useChannels) {
            \CAdminNotify::Add([
                'MESSAGE' => Loc::getMessage('MEDIACA_CROSSPOSTING_TASK_ADDER_TASKS', ['#CHANNELS#' => implode(', ', $useChannels)]),
                'NOTIFY_TYPE' => \CAdminNotify::TYPE_NORMAL,
                'ENABLE_CLOSE' => 'Y',
            ]);
        }
    }
}
