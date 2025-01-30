<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\LevelSetList;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/admin',
        __DIR__ . '/install',
        __DIR__ . '/lang',
        __DIR__ . '/lib',
    ])
    ->withSets([LevelSetList::UP_TO_PHP_81])
    ->withPreparedSets(
        deadCode: true,
        typeDeclarations: true,
    );
