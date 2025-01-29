<?php

declare(strict_types=1);

namespace Mediaca\Crossposting\Vk\Id;

readonly class AccessTokens
{
    public function __construct(
        public string $accessToken,
        public string $refreshToken,
        public string $idToken,
        public string $deviceId,
    ) {}
}
