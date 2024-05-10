<?php

namespace Stoyantodorov\ApiClient;

use Illuminate\Http\Client\Response;
use SensitiveParameter;
use Stoyantodorov\ApiClient\Data\TokenData;
use Stoyantodorov\ApiClient\Data\RefreshTokenData;
use Stoyantodorov\ApiClient\Interfaces\HttpClientInterface;
use Stoyantodorov\ApiClient\Interfaces\TokenInterface;

class Token implements TokenInterface
{
    public function __construct(
                              protected HttpClientInterface $httpClient,
        #[SensitiveParameter] protected TokenData           $tokenData,
        #[SensitiveParameter] protected RefreshTokenData    $refreshTokenData,
        #[SensitiveParameter] protected string|null         $token = null,
                              protected int                 $retries = 3,
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

    protected function requestToken(): string
    {
        $response = $this->request(
            url: $this->tokenData->url,
            body: $this->tokenData->body,
            headers: $this->tokenData->headers,
            method: $this->tokenData->method
        );

        return $this->processSuccessResponse($response, 'tokenData');
    }

    protected function refreshToken(): string
    {
        $response = $this->request(
            url: $this->refreshTokenData->url,
            body: $this->refreshTokenData->body,
            headers: $this->refreshTokenData->headers,
            method: $this->refreshTokenData->method
        );

        return $this->processSuccessResponse($response, 'refreshTokenData');
    }

    protected function request(string $url, array $body, array $headers, string $method): Response
    {
        return $this->httpClient
            ->baseConfig(headers: $headers, retries: $this->retries)
            ->{$method}($url, $body);
    }

    protected function processSuccessResponse(Response $response, string $dataName): string
    {
        $token = $response->json();
        foreach ($this->{$dataName}->responseNestedKeys as $key) {
            $token = $token[$key];
        }
        $this->token = $token;

        return $this->token;
    }
}
