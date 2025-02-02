<?php

declare(strict_types=1);

namespace Mediaca\Crossposting\Iblock;

use Bitrix\Main\Loader;
use CIBlockElement;

class ElementGateway
{
    public function require(array $select, array $filter): ?array
    {
        $select = array_merge(
            $select,
            [
                'ID',
                'IBLOCK_ID',
                'DETAIL_PAGE_URL',
            ],
        );

        $result = CIBlockElement::GetList([], $filter, false, ['nPageSize' => 1], $select);

        return $result->GetNext(false, false)
            ?? throw new \RangeException("No selected element with filter: " . json_encode($filter, JSON_THROW_ON_ERROR));
    }
}
