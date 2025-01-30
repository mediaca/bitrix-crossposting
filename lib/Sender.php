<?php

declare(strict_types=1);

namespace Mediaca\Crossposting;

interface Sender
{
    public function send(array $data, array $photos);
}
