<?php

declare(strict_types=1);

namespace Mediaca\Crossposting\Iblock;

class IblockTypeGateway
{
    /**
     * @return array<int, array{id: string, name: string}>
     */
    public function fetchAll(): array
    {
        $dbTypes = \CIBlockType::GetList(['SORT' => 'ASC', 'ID' => 'ASC']);
        $types = [];
        while ($type = $dbTypes->Fetch()) {
            $lang = \CIBlockType::GetByIDLang($type['ID'], LANGUAGE_ID);

            $types[] = [
                'id' => $type['ID'],
                'name' => $lang['NAME'],
            ];
        }

        return $types;
    }
}
