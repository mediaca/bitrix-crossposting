<?php

declare(strict_types=1);

namespace Mediaca\Crossposting;

use Bitrix\Main\Config\Configuration;
use Mediaca\Crossposting\Task\TaskGateway;

class SenderAgent
{
    public static function run(): string
    {
        $tasks = TaskGateway::fetchUnExecTasks(20);
        $config = Configuration::getValue(Module::ID);

        foreach ($tasks as $task) {
            $sender = SenderFactory::build($task['channel'], $config);

            // @todo Обновление отправителя ВК при истекшем сроке токена доступа
            $sender->send($task['elementId']);
        }

        return '\\' . __METHOD__ . '();';
    }
}
