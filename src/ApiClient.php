<?php

namespace Stoyantodorov\ApiClient;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Stoyantodorov\ApiClient\Enum\PendingRequestMethod;
use Stoyantodorov\ApiClient\Enum\HttpRequestFormat;
use Stoyantodorov\ApiClient\Enum\HttpRequestMethod;
use Stoyantodorov\ApiClient\Enum\RequestMethod;

class ApiClient
{
    protected PendingRequest|null $pendingRequest;

    public function addPendingRequestMethod(PendingRequestMethod $method, array $parameters = [], bool $newPendingRequest = false): static
    {
        $this->pendingRequest = $this->getPendingRequest($method, $parameters, $newPendingRequest);

        return $this;
    }

    public function config(
        array $headers = [],
        int $retries = 1,
        int $retryInterval = 1000,
        int $timeout = 30,
        int $connectTimeout = 3,
        string|null $userAgent = null,
        bool $newPendingRequest = true,
    ): self
    {
        $this->pendingRequest = $this->getPendingRequest(
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

    public function withBasicAuth(string $username, string $password): self
    {
        $this->pendingRequest->withBasicAuth($username, $password);

        return $this;
    }

    public function withDigestAuth(string $username, string $password): self
    {
        $this->pendingRequest->withBasicAuth($username, $password);

        return $this;
    }

    public function send(HttpRequestMethod $method, string $url, array $options = [], HttpRequestFormat|null $format = null): Response
    {
        $this->sendRequest(RequestMethod::SEND, $url, $this->requestOptions($options, $method, $format), $format);
    }

    public function head(string $url, array $parameters = [],): Response|null
    {
        $this->sendRequest(RequestMethod::HEAD, $url, $parameters);
    }

    public function get(string $url, array $parameters = [],): Response|null
    {
        $this->sendRequest(RequestMethod::GET, $url, $parameters);
    }

    public function post(string $url, array $body = []): Response|null
    {
        $this->sendRequest(RequestMethod::POST, $url, $body);
    }

    public function put(string $url, array $body = []): Response|null
    {
        $this->sendRequest(RequestMethod::PUT, $url, $body);
    }

    public function patch(string $url, array $body = []): Response|null
    {
        $this->sendRequest(RequestMethod::PATCH, $url, $body);
    }

    public function delete(string $url, array $body = []): Response|null
    {
        $this->sendRequest(RequestMethod::DELETE, $url, $body);
    }

    protected function getPendingRequest(PendingRequestMethod $pendingRequestMethod, array $parameters = [], bool $newPendingRequest = true): PendingRequest
    {
        $method = $pendingRequestMethod->value;

        if ($newPendingRequest || ! $this->pendingRequest) {
            return Http::$method(...$parameters);
        }

        return $this->pendingRequest->$method(...$parameters);
    }

    public function sendRequest(
        RequestMethod          $requestMethod,
        string                 $url,
        array                  $options,
        HttpRequestMethod|null $httpRequestMethod = null,
    ): Response|null
    {
        try {
            return $this->getResponse($requestMethod, $url, $options)->throw();
        } catch (RequestException $exception) {
            $method = $httpRequestMethod ?? strtoupper($requestMethod->value);

            return $this->processRequestException($exception, "Failed HTTP {$method} request to {$url}");
        } catch (ConnectionException $e) {
            return $this->processConnectionException("Failed to connect to {$url}");
        }
    }

    public function getResponse(RequestMethod $requestMethod,string $url, array $options): Response
    {
        $methodToUse = $requestMethod->value;

        return $this->pendingRequest->$methodToUse($url, $options);
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

    protected function getRequestFormat(HttpRequestMethod $method, HttpRequestFormat|null $format): string
    {
        return match ($method) {
            HttpRequestMethod::HEAD, HttpRequestMethod::GET => HttpRequestFormat::QUERY->value,
            default => $format->value,
        };
    }

    protected function requestOptions(array $options, HttpRequestMethod $method, HttpRequestFormat|null $format): array
    {
        if ($options) {
            return [$this->getRequestFormat($method, $format) => $options];
        }

        return [];
    }
}
