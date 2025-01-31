<?php

declare(strict_types=1);

namespace Mediaca\Crossposting;

use Bitrix\Main\Config\Configuration;
use Mediaca\Crossposting\Iblock\ElementGateway;
use Mediaca\Crossposting\Task\TaskGateway;

class SenderAgent
{
    public static function run(): string
    {
        $tasks = TaskGateway::fetchUnExecTasks(20);
        $config = Configuration::getValue('mediaca.crossposting');
        $gateway = new ElementGateway();

        foreach ($tasks as $task) {
            $senderFactory = new SenderFactory($task['channel'], $config);

            $dataCodes = array_merge($senderFactory->getTemplateParser()->getDataCodes(), $senderFactory->getDataPhotos());
            $filter = [
                'ID' => $task['elementId'],
                'ACTIVE' => 'Y',
            ];

            // @todo Обновление отправителя ВК при истекшем сроке токена доступа
            // @todo решить, что делать если не найден элемент
            $element = $gateway->getElement($dataCodes, $filter);
            $photos = self::getFiles($element, $senderFactory->getDataPhotos());

            $senderFactory->getSender()->send($element, $photos);
        }

        return '\\' . __METHOD__ . '();';
    }

    private static function getFiles(array $element, array $dataCodes): array
    {
        $dataCodes = array_filter($dataCodes, static fn($code) => $element[$code]);
        if (!$dataCodes) {
            return [];
        }

        $ids = [];
        foreach ($dataCodes as $code) {
            $value = $element[$code];
            if (is_array($value)) {
                $ids = array_merge($ids, $value);
            } else {
                $ids[] = $value;
            }
        }

        $rawFiles = [];
        $db = \CFile::GetList([], ['@id' => implode(',', $ids)]);
        while ($rawFile = $db->Fetch()) {
            $rawFile['SRC'] = \CFile::GetFileSRC($rawFile);

            $rawFiles[$rawFile['ID']] = $rawFile;
        }

        $result = [];
        foreach ($dataCodes as $code) {
            $value = $element[$code];

            if (!is_array($value)) {
                $result[] = $rawFiles[$value];

                continue;
            }

            foreach ($value as $valueItem) {
                $result[] = $rawFiles[$valueItem];
            }
        }

        return $result;
    }
}
