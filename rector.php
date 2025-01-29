<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/admin',
        __DIR__ . '/install',
        __DIR__ . '/lang',
        __DIR__ . '/lib',
    ])
    ->withDowngradeSets(php81: true)
    ->withPreparedSets(
        deadCode: true,
        typeDeclarations: true,
    );