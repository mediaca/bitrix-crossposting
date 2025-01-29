<?php

declare(strict_types=1);

namespace ALS\Crossposting\Vk\Id;


use ALS\Crossposting\Exception\RequestFailException;
use Bitrix\Main\Web\Http\FormStream;
use Bitrix\Main\Web\Http\Request;
use Bitrix\Main\Web\Uri;
use Psr\Http\Client\ClientInterface;


readonly class VkIdClient
{
    private const BASE_URL = 'https://id.vk.com/';


    public function __construct(public ClientInterface $client, public int $clientId) {}


    /**
     * @param Scope[] $scopes
     * @param string $redirectUri
     * @param CodeVerifier $codeVerifier
     * @param State|null $state
     * @return string
     */
    public function getAuthorizeUrl(
        array $scopes,
        string $redirectUri,
        CodeVerifier $codeVerifier,
        ?State $state,
    ): string {
        $params = [
            'response_type'         => 'code',
            'client_id'             => $this->clientId,
            'scope'                 => implode(' ', array_map(static fn($scope) => $scope->value, $scopes)),
            'redirect_uri'          => $redirectUri,
            'code_challenge'        => $codeVerifier->getChallenge(),
            'code_challenge_method' => $codeVerifier->method->value,
            'state'                 => $state?->value,
        ];

        return self::BASE_URL . "authorize?" . http_build_query($params);
    }


    /**
     * @return array{
     *      refresh_token: string,
     *      access_token: string,
     *      id_token: string,
     *      token_type: string,
     *      expires_in: int,
     *      user_id: int,
     *      state: string|null,
     *      scope: string
     * }
     */
    public function getAccessToken(
        string $codeVerifier,
        string $code,
        string $deviceId,
        string $redirectUri,
        ?State $state,
    ): array {
        $data = [
            'client_id'     => $this->clientId,
            'code_verifier' => $codeVerifier,
            'grant_type'    => 'authorization_code',
            'code'          => $code,
            'device_id'     => $deviceId,
            'redirect_uri'  => $redirectUri,
            'state'         => $state?->value,
        ];

        return $this->send('POST', self::BASE_URL . '/oauth2/auth', $data);
    }


    /**
     * @return array{
     *      refresh_token: string,
     *      access_token: string,
     *      token_type: string,
     *      expires_in: int,
     *      user_id: int,
     *      state: string|null,
     *      scope: string
     * }
     */
    public function updateAccessToken(string $refreshToken, string $deviceId, ?State $state): array
    {
        $data = [
            'client_id'     => $this->clientId,
            'grant_type'    => 'refresh_token',
            'refresh_token' => $refreshToken,
            'device_id'     => $deviceId,
            'state'         => $state?->value,
        ];

        return $this->send('POST', self::BASE_URL . '/oauth2/auth', $data);
    }


    private function send(string $method, string $url, array $data): array
    {
        $request = new Request($method, new Uri($url), [], new FormStream($data));
        $response = $this->client->sendRequest($request);
        if ($response->getStatusCode() !== 200) {
            throw new RequestFailException("The request \"$url\" failed with the status: {$response->getStatusCode()}");
        }

        $content = (string)$response->getBody();
        $decodedContent = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        if (array_key_exists('error', $decodedContent)) {
            throw new RequestFailException("The request \"$url\" failed with an error: $content");
        }

        return $decodedContent;
    }
}
