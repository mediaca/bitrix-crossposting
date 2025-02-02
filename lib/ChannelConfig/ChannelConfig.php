<?php

declare(strict_types=1);

namespace Mediaca\Crossposting\ChannelConfig;

interface ChannelConfig
{
    public function getValues(): array;

    public function isFilledRequiredFields(): bool;
}
