<?php

declare(strict_types=1);

namespace Mediaca\Crossposting\Vk\Api;

use Mediaca\Crossposting\Exception\RequestFailException;
use Mediaca\Crossposting\Exception\UserAuthorizationFailException;
use Mediaca\Crossposting\Vk\Api\WallPost\Attachments;
use Bitrix\Main\IO\File;
use Bitrix\Main\Web\Http\FormStream;
use Bitrix\Main\Web\Http\MultipartStream;
use Bitrix\Main\Web\Http\Request;
use Bitrix\Main\Web\Uri;
use Psr\Http\Client\ClientInterface;

class VkontakteApiClient
{
    private const BASE_URL = 'https://api.vk.com/method/';
    private const VERSION = '5.199';


    public function __construct(public readonly ClientInterface $client, public readonly string $accessToken) {}


    public function wallPost(int $ownerId, bool $fromGroup, ?string $message, ?Attachments $attachments): array
    {
        $data = [
            'owner_id' => $ownerId,
            'from_group' => $fromGroup ? '1' : '0',
            'message' => $message,
        ];

        if ($attachments) {
            $data['attachments'] = $attachments->getRequestValue();
            $data = array_merge($attachments->getAdditionParams(), $data);
        }

        return $this->sendRequest('POST', 'wall.post', $data);
    }


    /**
     * @return array{album_id: int, upload_url: string, user_id: int}
     */
    public function getWallUploadServer(int $groupId): array
    {
        $data = ['group_id' => abs($groupId)];

        return $this->sendRequest('POST', 'photos.getWallUploadServer', $data)['response'];
    }


    /**
     * @return array{server: int, photo: array, hash: string}
     */
    public function uploadPhoto(string $uploadServerUrl, string $photoPath): array
    {
        $file = new File($photoPath);
        if (!$file->isExists()) {
            throw new \RangeException("File not found at path $photoPath");
        }

        $data = [
            'photo' => [
                'contentType' => $file->getContentType(),
                'filename'    => $file->getName(),
                'resource'    => $file->open('r'),
            ],
        ];

        $body = new MultipartStream($data);
        $request = new Request(
            'POST',
            new Uri($uploadServerUrl),
            ['Content-type' => 'multipart/form-data; boundary=' . $body->getBoundary()],
            body: $body,
        );

        $response = (string) $this->client->sendRequest($request)->getBody();

        $response = json_decode($response, true, 512, JSON_THROW_ON_ERROR);
        $photo = json_decode((string) $response['photo'], true, 512, JSON_THROW_ON_ERROR);
        if (!$photo) {
            throw new RequestFailException("failed to upload photo $photoPath");
        }

        return $response;
    }


    /**
     * @return array{
     *     album_id: int,
     *     date: int,
     *     id: int,
     *     owner_id: int,
     *     access_key: string,
     *     sizes: array,
     *     text: string|null,
     *     web_view_token: string,
     *     has_tags: bool,
     *     orig_photo: array
     * }
     */
    public function saveWallPhoto(int $ownerId, array $responseUploadPhoto, string $caption = null): array
    {
        $responseUploadPhoto['caption'] = $caption;
        if ($ownerId > 1) {
            $responseUploadPhoto['user_id'] = $ownerId;
        } else {
            $responseUploadPhoto['group_id'] = abs($ownerId);
        }

        return $this->sendRequest('POST', 'photos.saveWallPhoto', $responseUploadPhoto)['response'][0];
    }


    private function sendRequest(string $method, string $action, array $data): array
    {
        $data = array_merge(
            [
                'access_token' => $this->accessToken,
                'v'            => self::VERSION,
            ],
            $data,
        );

        $request = new Request($method, new Uri(self::BASE_URL . $action), [], new FormStream($data));
        $response = $this->client->sendRequest($request);
        if ($response->getStatusCode() !== 200) {
            throw new RequestFailException(
                "The action \"$action\" failed with the status: {$response->getStatusCode()}",
            );
        }

        $content = (string) $response->getBody();
        $decodedContent = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        if (array_key_exists('error', $decodedContent)) {
            $message = "The action \"$action\" failed with an error: $content";

            if ($decodedContent['error']['error_code'] === 5) {
                throw new UserAuthorizationFailException($message);
            }

            throw new RequestFailException($message);
        }

        return $decodedContent;
    }


    public function uploadWallPhotoAndSave(int $ownerId, string $photoPath): array
    {
        $server = $this->getWallUploadServer($ownerId);
        $uploadResponse = $this->uploadPhoto($server['upload_url'], $photoPath);

        return $this->saveWallPhoto($ownerId, $uploadResponse);
    }
}
