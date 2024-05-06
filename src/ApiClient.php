<?php

namespace Stoyantodorov\ApiClient;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Stoyantodorov\ApiClient\Enums\PendingRequestMethod;
use Stoyantodorov\ApiClient\Enums\HttpRequestFormat;
use Stoyantodorov\ApiClient\Enums\HttpMethod;
use Stoyantodorov\ApiClient\Enums\ApiClientRequestMethod;
use Stoyantodorov\ApiClient\Interfaces\ApiClientInterface;
use function PHPUnit\Framework\matches;

class ApiClient implements ApiClientInterface
{
    protected PendingRequest|null $pendingRequest = null;

    public function baseConfig(
        array $headers = [],
        int $retries = 1,
        int $retryInterval = 1000,
        int $timeout = 30,
        int $connectTimeout = 3,
        string|null $userAgent = null,
        bool $newPendingRequest = true,
    ): self
    {
        $this->pendingRequest = $this->pendingRequest(
                pendingRequestMethod:  PendingRequestMethod::RETRY,
                parameters: [$retries, $retryInterval],
                newPendingRequest: $newPendingRequest,
            )
            ->withHeaders($headers)
            ->timeout($timeout)
            ->connectTimeout($connectTimeout)
            ->withUserAgent($userAgent ?? config('app.name'));

        return $this;
    }

    public function addPendingRequestMethod(
        PendingRequestMethod $method,
        array $parameters = [],
        bool $newPendingRequest = false,
    ): self
    {
        $this->pendingRequest = $this->pendingRequest($method, $parameters, $newPendingRequest);

        return $this;
    }

    public function withBasicAuth(string $username, string $password): self
    {
        $this->pendingRequest = $this->pendingRequest(
            pendingRequestMethod:  PendingRequestMethod::WITH_BASIC_AUTH,
            parameters: [$username, $password],
            newPendingRequest: false,
        );

        return $this;
    }

    public function withDigestAuth(string $username, string $password): self
    {
        $this->pendingRequest = $this->pendingRequest(
            pendingRequestMethod:  PendingRequestMethod::WITH_DIGEST_AUTH,
            parameters: [$username, $password],
            newPendingRequest: false,
        );

        return $this;
    }

    public function send(
        HttpMethod        $httpMethod,
        string            $url,
        HttpRequestFormat $format,
        array             $options = [],
    ): Response|null
    {
        return $this->sendRequest(
            apiClientRequestMethod: ApiClientRequestMethod::SEND,
            url: $url,
            options: [$format->value => $options],
            httpMethod: $httpMethod,
        );
    }

    public function head(string $url, array $parameters = [],): Response|null
    {
        $this->addPendingRequestMethod(PendingRequestMethod::WITH_QUERY_PARAMETERS, [$parameters]);

        return $this->sendRequest(ApiClientRequestMethod::HEAD, $url);
    }

    public function get(string $url, array $parameters = [],): Response|null
    {
        $this->addPendingRequestMethod(PendingRequestMethod::WITH_QUERY_PARAMETERS, [$parameters]);

        return $this->sendRequest(ApiClientRequestMethod::GET, $url);
    }

    public function post(string $url, array $body = []): Response|null
    {
        return $this->sendRequest(ApiClientRequestMethod::POST, $url, $body);
    }

    public function put(string $url, array $body = []): Response|null
    {
        return $this->sendRequest(ApiClientRequestMethod::PUT, $url, $body);
    }

    public function patch(string $url, array $body = []): Response|null
    {
        return $this->sendRequest(ApiClientRequestMethod::PATCH, $url, $body);
    }

    public function delete(string $url, array $body = []): Response|null
    {
        return $this->sendRequest(ApiClientRequestMethod::DELETE, $url, $body);
    }

    public function sendRequest(
        ApiClientRequestMethod $apiClientRequestMethod,
        string                 $url,
        array                  $options = [],
        HttpMethod|null        $httpMethod = null,
    ): Response|null
    {
        try {
            return $this->getResponse($apiClientRequestMethod, $url, $options, $httpMethod, true);
        } catch (RequestException $exception) {
            $method = $httpMethod ? $httpMethod->value : strtoupper($apiClientRequestMethod->value);

            return $this->processRequestException($exception, "Failed HTTP {$method} request to {$url}");
        } catch (ConnectionException $e) {
            return $this->processConnectionException("Failed to connect to {$url}");
        }
    }

    public function getResponse(
        ApiClientRequestMethod $apiClientMethod,
        string $url,
        array $options = [],
        HttpMethod|null $httpMethod = null,
        bool $throw  = false,
    ): Response
    {
        $methodToUse = $apiClientMethod->value;
        $parameters = match ($apiClientMethod) {
            ApiClientRequestMethod::SEND => [$httpMethod->value, $url, $options],
            default => [$url, $options],
        };
        $response = $this->pendingRequest ?
            $this->pendingRequest->{$methodToUse}(...$parameters) :
            Http::$methodToUse(...$parameters);

        return $throw ? $response->throw() : $response;
    }

    public function getPendingRequest(): PendingRequest|null
    {
        return $this->pendingRequest;
    }

    protected function pendingRequest(PendingRequestMethod $pendingRequestMethod, array $parameters = [], bool $newPendingRequest = true): PendingRequest
    {
        $method = $pendingRequestMethod->value;

        if ($newPendingRequest || ! $this->pendingRequest) {
            return Http::{$method}(...$parameters);
        }

        return $this->pendingRequest->{$method}(...$parameters);
    }

    protected function processRequestException(RequestException $exception, string $message): Response
    {
        Log::critical($message);
        Log::critical("Response status: {$exception->response->status()}");
        Log::critical($exception->response->body());

        return $exception->response;
    }

    protected function processConnectionException(string $message): null
    {
        Log::critical($message);

        return null;
    }

    protected function getRequestFormat(HttpMethod $method, HttpRequestFormat|null $format): string
    {
        return match ($method) {
            HttpMethod::HEAD, HttpMethod::GET => HttpRequestFormat::QUERY->value,
            default => $format->value,
        };
    }
}
