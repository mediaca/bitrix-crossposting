<?php

declare(strict_types=1);

namespace Mediaca\Crossposting\Iblock;

class IblockGateway
{
    /**
     * @return array<int, array{id: int, name: string, typeId: string}>
     */
    public function fetchAll(): array
    {
        $dbIblocks = \CIBlock::GetList(['SORT' => 'ASC', 'ID' => 'ASC']);

        $iblocks = [];
        while ($iblock = $dbIblocks->Fetch()) {

            $iblocks[] = [
                'id' => (int) $iblock['ID'],
                'name' => $iblock['NAME'],
                'typeId' => $iblock['IBLOCK_TYPE_ID'],
            ];
        }

        return $iblocks;
    }
}
