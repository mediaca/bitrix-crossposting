<?php

declare(strict_types=1);

namespace Mediaca\Crossposting\Telegram;

readonly class TextTelegramMessage
{

    private const ALLOWED_TAGS = [
        'b',
        'strong',
        'i',
        'em',
        'u',
        'ins',
        's',
        'strike',
        'del',
        'span',
        'tg-spoiler',
        'a',
        'tg-emoji',
        'code',
        'pre',
        'blockquote',
    ];


    public function __construct(private string $text, private string $endingText = '') {}


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


