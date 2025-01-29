<?php

declare(strict_types=1);

namespace Mediaca\Crossposting\Vk\Id;

enum Scope: string
{
    case WALL = 'wall';
    case PHOTOS = 'photos';
}
