<?php

declare(strict_types=1);

namespace Mediaca\Crossposting\Telegram;

class TextTelegramMessage
{
    public function __construct(private readonly string $text, private readonly string $endingText = '') {}


    public function getText(int $maxLength): string
    {
        $normalizedText = trim(self::normalize($this->text));
        $normalizedEndingText = self::normalize($this->endingText);

        $result = $normalizedText . $normalizedEndingText;
        if (mb_strlen($result) > $maxLength) {
            $result = mb_substr($normalizedText, 0, $maxLength - 3 - mb_strlen($normalizedEndingText))
                . '...' . $normalizedEndingText;
        }

        return $result;
    }


    private static function normalize(string $text): string
    {
        return str_replace(
            '\r\n\t',
            '',
            preg_replace('/^ +/m', '', strip_tags($text)),
        );
    }
}
