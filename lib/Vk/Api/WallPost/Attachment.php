<?php

declare(strict_types=1);

namespace ALS\Crossposting\Vk\Api\WallPost;

interface Attachment
{
    public function getRequestValue(): string;
}
