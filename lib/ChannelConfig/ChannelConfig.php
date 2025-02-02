<?php

declare(strict_types=1);

namespace Mediaca\Crossposting\Config;

interface ChannelConfig
{
    public function getValues(): array;

    public function isFilledRequiredFields(): bool;
}
