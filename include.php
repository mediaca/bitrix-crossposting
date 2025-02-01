<?php

declare(strict_types=1);

use Bitrix\Main\Loader;
use Mediaca\Crossposting\Module;

Loader::requireModule('iblock');

\CJSCore::RegisterExt(
    Module::ID,
    [
        'css' => '/bitrix/css/' . Module::ID . '/styles.css',
        'use' => \CJSCore::USE_ADMIN,
    ],
);
