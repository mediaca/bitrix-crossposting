<?php

declare(strict_types=1);

namespace Mediaca\Crossposting\Vk\Api\WallPost;

class LinkAttachment implements Attachment, AdditionParams
{
    public function __construct(private readonly string $url, private readonly string|null $title = null) {}


    public function getRequestValue(): string
    {
        return $this->url;
    }


    public function getAdditionParams(): array
    {
        return $this->title ? ['link_title' => $this->title] : [];
    }
}
