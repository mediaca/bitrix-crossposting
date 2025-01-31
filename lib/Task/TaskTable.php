<?php

declare(strict_types=1);

namespace Mediaca\Crossposting\Task;

use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\Entity\EnumField;
use Bitrix\Main\Entity\IntegerField;
use Bitrix\Main\ORM\Fields\DatetimeField;

// @todo задуматься о добавлении индексов
class TaskTable extends DataManager
{
    public static function getTableName(): string
    {
        return 'mediaca_crossposting_task';
    }

    public static function getMap(): array
    {
        return [
            new IntegerField(
                'ID',
                [
                    'autocomplete' => true,
                    'primary' => true,
                ],
            ),
            new DatetimeField('CREATED', ['required' => true]),
            new IntegerField('ELEMENT_ID', ['required' => true,]),
            new EnumField(
                'CHANNEL',
                [
                    'required' => true,
                    'values' => [
                        'vkontakte',
                        'telegram',
                    ],
                ],
            ),
            new EnumField(
                'STATUS',
                [
                    'values' => [
                        'error',
                        'success',
                    ],
                ],
            ),
            new DatetimeField('DATE_EXEC'),
        ];
    }
}
