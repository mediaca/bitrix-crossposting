<?php

declare(strict_types=1);

namespace Mediaca\Crossposting\ChannelConfig;

class VkChannelConfig implements ChannelConfig
{
    public readonly ?int $clientId;
    public readonly ?int $ownerId;
    public readonly bool $fromGroup;
    public readonly ?array $dataPhotos;
    public readonly bool $useAllPhotos;
    public readonly ?string $messageTemplate;
    public ?string $accessToken;
    public ?string $refreshToken;
    public ?string $idToken;
    public ?string $deviceId;

    public function __construct(array $data)
    {
        $this->clientId = $data['clientId'] ?? null;
        $this->ownerId = $data['ownerId'] ?? null;
        $this->fromGroup = $data['fromGroup'] ?? false;
        $this->dataPhotos = $data['dataPhotos'] ?? null;
        $this->useAllPhotos = $data['useAllPhotos'] ?? false;
        $this->messageTemplate = $data['messageTemplate'] ?? null;

        $this->accessToken = $data['accessToken'] ?? null;
        $this->refreshToken = $data['refreshToken'] ?? null;
        $this->idToken = $data['idToken'] ?? null;
        $this->deviceId = $data['deviceId'] ?? null;
    }

    public static function byFormData(array $formData): self
    {
        $data = [
            'clientId' => $formData['client_id'] ? (int) $formData['client_id'] : null,
            'ownerId' => $formData['owner_id'] ? (int) $formData['owner_id'] : null,
            'fromGroup' => (bool) $formData['from_group'],
            'dataPhotos' => $formData['data_photos'] ? array_map('trim', explode(',', $formData['data_photos'])) : null,
            'useAllPhotos' => (bool) $formData['all_photos'],
            'messageTemplate' => $formData['message_template'] ?? null,

            'accessToken' => $formData['access_token'] ?? null,
            'refreshToken' => $formData['refresh_token'] ?? null,
            'idToken' => $formData['id_token'] ?? null,
            'deviceId' => $formData['device_id'] ?? null,
        ];

        return new self($data);
    }

    public function getValues(): array
    {
        return [
            'ownerId' => $this->ownerId,
            'fromGroup' => $this->fromGroup,
            'dataPhotos' => $this->dataPhotos,
            'useAllPhotos' => $this->useAllPhotos,
            'messageTemplate' => $this->messageTemplate,
            'clientId' => $this->clientId,
            'accessToken' => $this->accessToken,
            'refreshToken' => $this->refreshToken,
            'idToken' => $this->idToken,
            'deviceId' => $this->deviceId,
        ];
    }

    public function isFilledRequiredFields(): bool
    {
        return (
            $this->clientId && $this->ownerId
            && $this->accessToken && $this->refreshToken && $this->idToken && $this->deviceId
            && ($this->dataPhotos || $this->messageTemplate)
        );
    }
}
