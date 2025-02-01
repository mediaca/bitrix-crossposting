<?php

declare(strict_types=1);

namespace Mediaca\Crossposting;

use Bitrix\Main\Config\Configuration;
use Bitrix\Main\Localization\Loc;
use Mediaca\Crossposting\Task\Channel;

class CrosspostingTab
{
    private const TABS_ID = 'mediaca_crossposting';
    private const TAB_TASK_ID = 'mediaca_crossposting_task';

    public static function getDescription(): array
    {
        return [
            'TABSET' => self::TABS_ID,
            'GetTabs' => self::getTabs(...),
            'ShowTab' => self::showTabContent(...),
        ];
    }

    public static function getTabs(array $element): array
    {
        $iblocks = Configuration::getValue(Module::ID)['main']['iblocks'] ?? [];
        if (!in_array((int) $element['IBLOCK']['ID'], $iblocks, true)) {
            return [];
        }

        return [
            [
                'DIV' => self::TAB_TASK_ID,
                'SORT' => 9999,
                'TAB' => Loc::getMessage('MEDIACA_CROSSPOSTING_TAB_TASK_TITLE'),
            ],
        ];
    }

    public static function showTabContent(string $tabId, array $element, $formData): void
    {
        foreach (Channel::cases() as $channel) {
            $name = self::getFieldNameChannel($channel);
            $title = Loc::getMessage('MEDIACA_CROSSPOSTING_TAB_TASK_CHANNEL_' . strtoupper($channel->value));

            // @todo блокировать чекбоксы с уведомлением о необходимости заполнить настройки

            echo "<tr>
                    <td width=\"40%\" class=\"adm-detail-valign-top adm-detail-content-cell-l\"><label for=\"$name\">$title</label></td>
                    <td width=\"60%\" class=\"adm-detail-content-cell-r\"><input type=\"checkbox\" name=\"$name\" id=\"$name\"></td>
            </tr>";
        }
    }

    public static function getFieldNameChannel(Channel $channel): string
    {

        return self::TAB_TASK_ID . "_$channel->value";
    }
}
