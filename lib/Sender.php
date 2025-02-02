<?php

declare(strict_types=1);

namespace Mediaca\Crossposting;

interface Sender
{
    public function send(int $elementId);
}
