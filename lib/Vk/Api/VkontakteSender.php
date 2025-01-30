<?php

declare(strict_types=1);

namespace Mediaca\Crossposting\Vk\Api;

use Mediaca\Crossposting\Template\TemplateParser;
use Mediaca\Crossposting\Vk\Api\WallPost\Attachments;
use Mediaca\Crossposting\Vk\Api\WallPost\LinkAttachment;
use Mediaca\Crossposting\Vk\Api\WallPost\MediaAttachment;
use Mediaca\Crossposting\Vk\Api\WallPost\TypeMediaAttachment;

class VkontakteSender
{
    public function __construct(
        private readonly VkontakteApiClient $client,
        private readonly TemplateParser $parser,
        private readonly array $settings,
        private readonly string $domain,
        private readonly string $documentRoot,
    ) {}

    public function send(array $data, ?array $photo): void
    {
        $message = $this->parser->build($data);

        $attachments = new Attachments();
        $attachments->addAttachment(new LinkAttachment(
            (str_starts_with('/', $data['DETAIL_PAGE_URL']) ? $this->domain : '') . $data['DETAIL_PAGE_URL'],
        ));

        if ($photo) {
            $photo = $this->client->uploadWallPhotoAndSave(
                $this->settings['ownerId'],
                $this->documentRoot . $photo['src'],
            );

            $attachments->addAttachment(
                new MediaAttachment(TypeMediaAttachment::PHOTO, $photo['owner_id'], $photo['id']),
            );
        }

        $this->client->wallPost(
            $this->settings['ownerId'],
            $this->settings['fromGroup'] ?? false,
            $message,
            $attachments,
        );
    }
}
