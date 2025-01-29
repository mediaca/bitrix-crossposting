<?php

declare(strict_types=1);

namespace Mediaca\Crossposting\Vk\Api\WallPost;

class Attachments
{
    private array $values = [];


    public function addAttachment(Attachment $attachment): void
    {
        $this->values[] = $attachment;
    }


    public function getRequestValue(): ?string
    {
        return $this->values ?
            implode(',', array_map(static fn($attachment) => $attachment->getRequestValue(), $this->values))
            : null;
    }


    public function getAdditionParams(): array
    {
        return array_reduce(
            $this->values,
            static fn($result, $attachment) => ($attachment instanceof AdditionParams) ?
                array_merge($result, $attachment->getAdditionParams()) : $result,
            [],
        );
    }
}
