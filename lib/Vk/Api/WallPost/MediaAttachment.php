<?php

declare(strict_types=1);

namespace Mediaca\Crossposting\Vk\Api\WallPost;

class MediaAttachment implements Attachment
{
    public function __construct(private readonly TypeMediaAttachment $type, private readonly int $ownerId, private readonly int $mediaId) {}


    public function getRequestValue(): string
    {
        return "{$this->type->value}{$this->ownerId}_$this->mediaId";
    }
}
