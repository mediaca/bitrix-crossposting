<?php

declare(strict_types=1);

namespace ALS\Crossposting\Vk\Id;

class State
{
    public const MIN_LENGTH = 32;

    public readonly string $value;


    public function __construct(int $length = 32)
    {
        if ($length < self::MIN_LENGTH) {
            throw new \DomainException('Length cannot be less than ' . self::MIN_LENGTH . ' characters');
        }

        $halfLength = $length / 2;
        $value = bin2hex(random_bytes((int)$halfLength));

        $this->value = is_float($halfLength) ? mb_substr($value, 0, -1) : $value;
    }
}
