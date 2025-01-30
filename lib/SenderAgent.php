<?php

declare(strict_types=1);

namespace Mediaca\Crossposting;

use Bitrix\Main\Config\Configuration;
use Mediaca\Crossposting\Iblock\ElementGateway;

class SenderAgent
{
    public static function run(): string
    {
        // @todo получать таски из шлюза
        $tasks = [
            [
                'elementId' => 1,
                'channel'   => Channel::VK,
            ],
        ];

        $config = Configuration::getValue('mediaca.crossposting');
        $gateway = new ElementGateway();

        foreach ($tasks as $task) {
            $senderFactory = new SenderFactory($task['channel'], $config);

            $filter = [
                'ID'     => $task['elementId'],
                'ACTIVE' => 'Y',
            ];

            // @todo получение фотографий

            $element = $gateway->getElement($senderFactory->getTemplateParser()->getDataCodes(), $filter);

            $senderFactory->getSender()->send($element, []);
        }

        return '\\' . __METHOD__ . '();';
    }
}
