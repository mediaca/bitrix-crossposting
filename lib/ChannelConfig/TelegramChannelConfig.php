<?php

declare(strict_types=1);

namespace Mediaca\Crossposting\ChannelConfig;

class TelegramChannelConfig implements ChannelConfig
{
    public readonly ?string $accessToken;
    public readonly ?string $chatUserName;
    public readonly ?array $dataPhotos;
    public readonly ?string $messageTemplate;

    public function __construct(array $data)
    {
        $this->accessToken = $data['accessToken'] ?? null;
        $this->chatUserName = $data['chatUserName'] ?? null;
        $this->dataPhotos = $data['dataPhotos'] ?? null;
        $this->messageTemplate = $data['messageTemplate'] ?? null;
    }

    public static function byFormData(array $formData): self
    {
        $data = [
            'accessToken' => $formData['access_token'] ?? null,
            'chatUserName' => $formData['chat_user_name'] ?? null,
            'dataPhotos' => $formData['data_photos'] ? array_map('trim', explode(',', $formData['data_photos'])) : null,
            'messageTemplate' => $formData['message_template'] ?? null,
        ];

        return new self($data);
    }

    public function getValues(): array
    {
        return [
            'accessToken' => $this->accessToken,
            'chatUserName' => $this->chatUserName,
            'dataPhotos' => $this->dataPhotos,
            'messageTemplate' => $this->messageTemplate,
        ];
    }

    public function isFilledRequiredFields(): bool
    {
        return (
            $this->accessToken && $this->chatUserName
            && ($this->dataPhotos || $this->messageTemplate)
        );
    }
}
