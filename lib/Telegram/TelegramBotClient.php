<?php

declare(strict_types=1);

namespace ALS\Crossposting\Telegram;


use ALS\Crossposting\Exception\RequestFailException;
use Bitrix\Main\IO\File;
use Bitrix\Main\Web\Http\FormStream;
use Bitrix\Main\Web\Http\MultipartStream;
use Bitrix\Main\Web\Http\Request;
use Bitrix\Main\Web\Uri;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;


class TelegramBotClient
{
    private const BASE_URL = 'https://api.telegram.org/bot';


    public function __construct(
        private readonly ClientInterface $client,
        private readonly string $token,
        private readonly string $chatUserName
    ) {}


    public function sendMessage(TextTelegramMessage $text): array
    {
        $body = new FormStream(
            [
                'chat_id'              => $this->chatUserName,
                'link_preview_options' => json_encode(
                    [
                        'prefer_large_media' => true,
                        'show_above_text'    => true,
                    ],
                    JSON_THROW_ON_ERROR
                ),
                'parse_mode'           => 'HTML',
                'text'                 => $text->getText(4096),
            ]
        );

        $request = new Request('POST', new Uri($this->getUrl('sendMessage')), body: $body);

        return $this->sendRequest($request);
    }


    private function getUrl(string $action): string
    {
        return self::BASE_URL . "$this->token/$action";
    }


    private function sendRequest(RequestInterface $request): array
    {
        $response = $this->client->sendRequest($request);
        $content = (string)$response->getBody();

        if ($response->getStatusCode() !== 200) {
            throw new RequestFailException(
                "The request \"{$request->getUri()}\" failed with the status: {$response->getStatusCode()}.\n$content"
            );
        }

        $decodedContent = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        if ($decodedContent['ok'] !== true) {
            throw new RequestFailException('Error send telegram message: ' . $content);
        }

        return $decodedContent;
    }


    public function sendPhoto(string $photoPath, TextTelegramMessage $caption = null): array
    {
        $photo = new File($photoPath);
        if (!$photo->isExists()) {
            throw new \RangeException("File not found at path $photoPath");
        }

        $body = new MultipartStream(
            [
                'chat_id'                  => $this->chatUserName,
                'photo'                    => [

                    'contentType' => $photo->getContentType(),
                    'filename'    => $photo->getName(),
                    'resource'    => $photo->open('r'),
                ],
                'parse_mode'               => 'HTML',
                'caption'                  => $caption?->getText(1024),
                'show_caption_above_media' => 'false',
            ]
        );

        $headers = ['Content-type' => 'multipart/form-data; boundary=' . $body->getBoundary()];

        $request = new Request('POST', new Uri($this->getUrl('sendPhoto')), $headers, $body);

        return $this->sendRequest($request);
    }
}
