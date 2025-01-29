<?php

declare(strict_types=1);

namespace ALS\Crossposting\Vk\Id;

enum Scope: string
{
    case WALL = 'wall';
    case PHOTOS = 'photos';
}
