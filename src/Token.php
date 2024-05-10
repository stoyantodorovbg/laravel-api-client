<?php

namespace Stoyantodorov\ApiClient;

use Illuminate\Http\Client\Response;
use SensitiveParameter;
use Stoyantodorov\ApiClient\Data\TokenRequestData;
use Stoyantodorov\ApiClient\Data\RefreshTokenRequestData;
use Stoyantodorov\ApiClient\Interfaces\HttpClientInterface;
use Stoyantodorov\ApiClient\Interfaces\TokenInterface;

class Token implements TokenInterface
{
    public function __construct(
                              protected HttpClientInterface     $httpClient,
        #[SensitiveParameter] protected TokenRequestData        $tokenRequestData,
        #[SensitiveParameter] protected RefreshTokenRequestData $refreshTokenRequestData,
        #[SensitiveParameter] protected string|null             $token = null,
                              protected int                     $retries = 3,
    )
    {
    }

    public function get(bool $refresh = false): string
    {
        if ($refresh) {
            $this->refreshToken();
        }

        if (! $this->token) {
            $this->requestToken();
        }

        return $this->token;
    }

    protected function requestToken(): void
    {
        $this->request(
            url: $this->tokenRequestData->url,
            body: $this->tokenRequestData->body,
            headers: $this->tokenRequestData->headers,
            method: $this->tokenRequestData->method
        );
    }

    protected function refreshToken(): void
    {
        $this->request(
            url: $this->refreshTokenRequestData->url,
            body: $this->refreshTokenRequestData->body,
            headers: $this->refreshTokenRequestData->headers,
            method: $this->refreshTokenRequestData->method
        );
    }

    protected function request(string $url, array $body, array $headers, string $method): Response
    {
        return $this->httpClient
            ->baseConfig(headers: $headers, retries: $this->retries)
            ->{$method}($url, $body);
    }

    protected function processSuccessResponse(Response $response): string
    {
        $responseBody = $response->json();
        $this->token = $responseBody['access_token'];

        return $this->token;
    }
}
