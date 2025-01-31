<?php

declare(strict_types=1);

namespace Mediaca\Crossposting\Task;

use Bitrix\Main\Type\DateTime;

class TaskGateway
{
    public static function add(DateTime $created, int $elementId, Channel $channel): void
    {
        $data = [
            'CREATED' => $created,
            'ELEMENT_ID' => $elementId,
            'CHANNEL' => $channel->value,
        ];

        $result = TaskTable::add($data);
        if (!$result->isSuccess()) {
            throw new \DomainException('Error adding task: ' . implode(', ', $result->getErrorMessages()));
        }
    }
}
