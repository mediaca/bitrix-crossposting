<?php

declare(strict_types=1);


use Bitrix\Main\Localization\Loc;


if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

global $USER;

Loc::loadMessages(__FILE__);

$menuLinks = [];

if ($USER->IsAdmin()) {
    $menuLinks = [
        'parent_menu' => 'global_menu_settings',
        'sort'        => 10,
        'text'        => Loc::getMessage('MEDIACA_CROSSPOSTING_MENU_SETTINGS_TITLE'),
        'items_id'    => 'mediaca.crossposting',
        'url'         => 'mediaca-crossposting-settings.php?lang=' . LANGUAGE_ID,
    ];
}

return $menuLinks;
