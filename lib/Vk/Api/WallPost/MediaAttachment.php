<?php

declare(strict_types=1);

namespace Mediaca\Crossposting\Vk\Api\WallPost;

readonly class MediaAttachment implements Attachment
{
    public function __construct(private TypeMediaAttachment $type, private int $ownerId, private int $mediaId) {}


    public function getRequestValue(): string
    {
        return "{$this->type->value}{$this->ownerId}_$this->mediaId";
    }
}
