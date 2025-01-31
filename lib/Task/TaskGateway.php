<?php

declare(strict_types=1);

namespace Mediaca\Crossposting\Task;

use Bitrix\Main\Type\DateTime;

class TaskGateway
{
    public static function add(DateTime $created, int $elementId, Channel $channel): void
    {
        $result = TaskTable::add([
            'CREATED' => $created,
            'ELEMENT_ID' => $elementId,
            'CHANNEL' => $channel->value,
        ]);

        if (!$result->isSuccess()) {
            throw new \DomainException('Error adding task: ' . implode(', ', $result->getErrorMessages()));
        }
    }

    public static function updateStatus(int $id, Status $status, DateTime $dateExec): void
    {
        $result = TaskTable::update(
            $id,
            [
                'STATUS' => $status->value,
                'DATE_EXEC' => $dateExec,
            ],
        );

        if (!$result->isSuccess()) {
            throw new \DomainException('Error updating task: ' . implode(', ', $result->getErrorMessages()));
        }
    }

    /**
     * @param int $limit
     * @return array<int, array{
     *     id: int,
     *     elementId: int,
     *     channel: Channel,
     * }>
     */
    public static function fetchUnExecTasks(int $limit): array
    {
        $tasks = TaskTable::getList([
            'select' => [
                'ID',
                'ELEMENT_ID',
                'CHANNEL',
            ],
        ])->fetchAll();

        return array_map(
            static fn($task) => [
                'id' => (int) $task['ID'],
                'elementId' => (int) $task['ELEMENT_ID'],
                'channel' => Channel::from($task['CHANNEL']),
            ],
            $tasks,
        );
    }
}
