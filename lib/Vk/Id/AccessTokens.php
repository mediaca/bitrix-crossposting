<?php

declare(strict_types=1);

namespace Mediaca\Crossposting\Vk\Id;

class AccessTokens
{
    public function __construct(
        public readonly string $accessToken,
        public readonly string $refreshToken,
        public readonly string $idToken,
        public readonly string $deviceId,
    ) {}
}
