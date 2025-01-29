<?php

declare(strict_types=1);

namespace ALS\Crossposting\Vk\Id;

class CodeVerifier
{
    public const MIN_LENGTH = 43;
    public const MAX_LENGTH = 128;

    private const SYMBOLS = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-_';

    public readonly string $value;


    public function __construct(
        int $length = self::MIN_LENGTH,
        public readonly CodeChallengeMethod $method = CodeChallengeMethod::S256,
    ) {
        if ($length < self::MIN_LENGTH || $length > self::MAX_LENGTH) {
            throw new \DomainException('Length must be between 32 and 128 characters');
        }

        $countSymbols = strlen(self::SYMBOLS);

        $value = '';
        for ($i = 0; $i < $length; $i++) {
            $value .= self::SYMBOLS[random_int(0, $countSymbols - 1)];
        }

        $this->value = $value;
    }


    public function getChallenge(): string
    {
        $hash = hash('sha256', $this->value, true);

        return sodium_bin2base64($hash, SODIUM_BASE64_VARIANT_URLSAFE_NO_PADDING);
    }
}