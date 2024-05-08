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
use Stoyantodorov\ApiClient\Enums\HttpClientRequestMethod;
use Stoyantodorov\ApiClient\Events\HttpConnectionFailed;
use Stoyantodorov\ApiClient\Events\HttpRequestFailed;
use Stoyantodorov\ApiClient\Events\HttpResponseSucceeded;
use Stoyantodorov\ApiClient\Interfaces\HttpClientInterface;

class HttpClient implements HttpClientInterface
{
    protected PendingRequest|null $pendingRequest = null;
    protected bool $eventOnSuccess;
    protected bool $eventOnRequestException;
    protected bool $eventOnConnectionException;
    protected bool $logOnRequestException;
    protected bool $logOnConnectionException;

    public function __construct()
    {
        $this->eventOnSuccess = config('api-client.events.onSuccess');
        $this->eventOnRequestException = config('api-client.events.onRequestException');
        $this->eventOnConnectionException = config('api-client.events.onConnectionException');
        $this->logOnRequestException = config('api-client.logs.onRequestException');
        $this->logOnConnectionException = config('api-client.logs.onConnectionException');
    }

    public function baseConfig(
        array               $headers = [],
        int                 $retries = 1,
        int                 $retryInterval = 1000,
        int                 $timeout = 30,
        int                 $connectTimeout = 3,
        string|null         $userAgent = null,
        PendingRequest|null $pendingRequest = null,
    ): self
    {
        $pendingRequest = $pendingRequest ? $pendingRequest->withHeaders($headers) : Http::withHeaders($headers);
        $pendingRequest->retry($retries);

        $pendingRequest = $this->pendingRequest(
                pendingRequestMethod:  PendingRequestMethod::RETRY,
                pendingRequest: $pendingRequest,
                parameters: [$retries, $retryInterval],
            )
            ->withHeaders($headers)
            ->timeout($timeout)
            ->connectTimeout($connectTimeout)
            ->withUserAgent($userAgent ?? config('app.name'));

        $this->setPendingRequest($pendingRequest);

        return $this;
    }

    public function send(
        HttpMethod          $httpMethod,
        string              $url,
        HttpRequestFormat   $format,
        array               $options = [],
        PendingRequest|null $pendingRequest = null
    ): Response|null
    {
        return $this->sendRequest(
            apiClientRequestMethod: HttpClientRequestMethod::SEND,
            url: $url,
            options: [$format->value => $options],
            pendingRequest: $pendingRequest,
            httpMethod: $httpMethod,
        );
    }

    public function head(string $url, array $parameters = [], PendingRequest|null $pendingRequest = null): Response|null
    {
        if ($parameters) {
            $pendingRequest = $this->pendingRequest(
                pendingRequestMethod:  PendingRequestMethod::WITH_QUERY_PARAMETERS,
                pendingRequest: $pendingRequest,
                parameters: compact('parameters'),
            );
        }

        return $this->sendRequest(apiClientRequestMethod: HttpClientRequestMethod::HEAD, url: $url, pendingRequest: $pendingRequest);
    }

    public function get(string $url, array $parameters = [], PendingRequest|null $pendingRequest = null): Response|null
    {
        if ($parameters) {
            $pendingRequest = $this->pendingRequest(
                pendingRequestMethod:  PendingRequestMethod::WITH_QUERY_PARAMETERS,
                pendingRequest: $pendingRequest,
                parameters: compact('parameters'),
            );
        }

        return $this->sendRequest(apiClientRequestMethod: HttpClientRequestMethod::GET, url: $url, pendingRequest: $pendingRequest);
    }

    public function post(string $url, array $body = [], PendingRequest|null $pendingRequest = null): Response|null
    {
        return $this->sendRequest(HttpClientRequestMethod::POST, $url, $body, $pendingRequest);
    }

    public function put(string $url, array $body = [], PendingRequest|null $pendingRequest = null): Response|null
    {
        return $this->sendRequest(HttpClientRequestMethod::PUT, $url, $body, $pendingRequest);
    }

    public function patch(string $url, array $body = [], PendingRequest|null $pendingRequest = null): Response|null
    {
        return $this->sendRequest(HttpClientRequestMethod::PATCH, $url, $body, $pendingRequest);
    }

    public function delete(string $url, array $body = [], PendingRequest|null $pendingRequest = null): Response|null
    {
        return $this->sendRequest(HttpClientRequestMethod::DELETE, $url, $body, $pendingRequest);
    }

    public function sendRequest(
        HttpClientRequestMethod $apiClientRequestMethod,
        string                  $url,
        array                   $options = [],
        PendingRequest|null     $pendingRequest = null,
        HttpMethod|null         $httpMethod = null,
    ): Response|null
    {
        $this->setPendingRequest($pendingRequest);
        try {
            $response = $this->request($apiClientRequestMethod, $url, $options, $httpMethod, true);

            return $this->processSuccessfulResponse($response, $url, $options);
        } catch (RequestException $exception) {
            $method = $httpMethod ? $httpMethod->value : strtoupper($apiClientRequestMethod->value);

            return $this->processRequestException($exception, "Failed HTTP {$method} request to {$url}", $url, $options);
        } catch (ConnectionException $exception) {
            return $this->processConnectionException($exception, "Failed to connect to {$url}", $url, $options);
        }
    }

    public function request(
        HttpClientRequestMethod $apiClientMethod,
        string                  $url,
        array                   $options = [],
        HttpMethod|null         $httpMethod = null,
        bool                    $throw = false,
    ): Response
    {
        $methodToUse = $apiClientMethod->value;
        $parameters = match ($apiClientMethod) {
            HttpClientRequestMethod::SEND => [$httpMethod->value, $url, $options],
            default => [$url, $options],
        };
        $response = $this->pendingRequest ?
            $this->pendingRequest->{$methodToUse}(...$parameters) :
            Http::$methodToUse(...$parameters);

        return $throw ? $response->throw() : $response;
    }

    public function setPendingRequest(PendingRequest|null $pendingRequest): self
    {
        if ($pendingRequest) {
            $this->pendingRequest = $pendingRequest;
        }

        return $this;
    }

    public function getPendingRequest(): PendingRequest|null
    {
        return $this->pendingRequest;
    }

    protected function pendingRequest(
        PendingRequestMethod $pendingRequestMethod,
        PendingRequest|null  $pendingRequest,
        array                $parameters = [],
    ): PendingRequest
    {
        $method = $pendingRequestMethod->value;

        if (! $pendingRequest && ! $this->pendingRequest) {
            return Http::{$method}(...$parameters);
        }

        if ($pendingRequest) {
            return $pendingRequest->{$method}(...$parameters);
        }

        return $this->pendingRequest->{$method}(...$parameters);
    }

    protected function processSuccessfulResponse(
        Response $response,
        string   $url,
        array    $options = [],
    ): Response
    {
        HttpResponseSucceeded::dispatchIf($this->eventOnSuccess, $response, $url, $options);

        return $response;
    }

    protected function processRequestException(
        RequestException $exception,
        string           $message,
        string           $url,
        array            $options = [],
    ): Response
    {
        HttpRequestFailed::dispatchIf($this->eventOnRequestException, $exception, $url, $options);

        if ($this->logOnRequestException) {
            Log::critical($message);
            Log::critical("Response status: {$exception->response->status()}");
            Log::critical($exception->response->body());
        }

        return $exception->response;
    }

    protected function processConnectionException(
        ConnectionException $exception,
        string              $message,
        string              $url,
        array               $options = [],
    ): null
    {
        HttpConnectionFailed::dispatchIf($this->eventOnConnectionException, $exception, $url, $options);

        if ($this->logOnConnectionException) {
            Log::critical($message);
        }

        return null;
    }

    public function fireEventOnSuccess(bool $value): self
    {
        $this->eventOnSuccess = $value;

        return $this;
    }

    public function fireEventOnRequestException(bool $value): self
    {
        $this->eventOnRequestException = $value;

        return $this;
    }

    public function fireEventOnConnectionException(bool $value): self
    {
        $this->eventOnConnectionException = $value;

        return $this;
    }

    public function logOnRequestException(bool $value): self
    {
        $this->logOnRequestException = $value;

        return $this;
    }

    public function logOnConnectionException(bool $value): self
    {
        $this->logOnConnectionException = $value;

        return $this;
    }

}
