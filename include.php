<?php

declare(strict_types=1);

use Bitrix\Main\Loader;

Loader::requireModule('iblock');

\CJSCore::RegisterExt(
    'mediaca.crossposting',
    [
        'css' => '/bitrix/css/mediaca.crossposting/styles.css',
        'use' => \CJSCore::USE_ADMIN,
    ],
);
