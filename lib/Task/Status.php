<?php

declare(strict_types=1);

namespace Mediaca\Crossposting\Task;

enum Status: string
{
    case ERROR = 'error';
    case SUCCESS = 'success';
}
