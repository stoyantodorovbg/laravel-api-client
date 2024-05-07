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
use Stoyantodorov\ApiClient\Events\HttpConnectionFailed;
use Stoyantodorov\ApiClient\Events\HttpRequestFailed;
use Stoyantodorov\ApiClient\Events\HttpResponseSucceeded;
use Stoyantodorov\ApiClient\Interfaces\ApiClientInterface;

class ApiClient implements ApiClientInterface
{
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
            $response = $this->getResponse($apiClientRequestMethod, $url, $options, $httpMethod, true);

            return $this->processSuccessfulResponse($response, $apiClientRequestMethod, $url, $options, $httpMethod);
        } catch (RequestException $exception) {
            $method = $httpMethod ? $httpMethod->value : strtoupper($apiClientRequestMethod->value);

            return $this->processRequestException($exception, "Failed HTTP {$method} request to {$url}", $apiClientRequestMethod, $url, $options, $httpMethod);
        } catch (ConnectionException $exception) {
            return $this->processConnectionException($exception, "Failed to connect to {$url}", $apiClientRequestMethod, $url, $options, $httpMethod);
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

    protected function pendingRequest(
        PendingRequestMethod $pendingRequestMethod,
        array $parameters = [],
        bool $newPendingRequest = true
    ): PendingRequest
    {
        $method = $pendingRequestMethod->value;

        if ($newPendingRequest || ! $this->pendingRequest) {
            return Http::{$method}(...$parameters);
        }

        return $this->pendingRequest->{$method}(...$parameters);
    }

    protected function processSuccessfulResponse(
        Response               $response,
        ApiClientRequestMethod $apiClientRequestMethod,
        string                 $url,
        array                  $options = [],
        HttpMethod|null        $httpMethod = null,
    ): Response
    {
        HttpResponseSucceeded::dispatchIf($this->eventOnSuccess, $response, $apiClientRequestMethod, $url, $options, $httpMethod);

        return $response;
    }

    protected function processRequestException(
        RequestException       $exception,
        string                 $message,
        ApiClientRequestMethod $apiClientRequestMethod,
        string                 $url,
        array                  $options = [],
        HttpMethod|null        $httpMethod = null,
    ): Response
    {
        HttpRequestFailed::dispatchIf($this->eventOnSuccess, $exception, $apiClientRequestMethod, $url, $options, $httpMethod);

        if ($this->logOnRequestException) {
            Log::critical($message);
            Log::critical("Response status: {$exception->response->status()}");
            Log::critical($exception->response->body());
        }

        return $exception->response;
    }

    protected function processConnectionException(
        ConnectionException    $exception,
        string                 $message,
        ApiClientRequestMethod $apiClientRequestMethod,
        string                 $url,
        array                  $options = [],
        HttpMethod|null        $httpMethod = null,
    ): null
    {
        HttpConnectionFailed::dispatchIf($this->eventOnSuccess, $exception, $apiClientRequestMethod, $url, $options, $httpMethod);

        if ($this->logOnConnectionException) {
            Log::critical($message);
        }

        return null;
    }

    public function setEventOnSuccess(bool $value): self
    {
        $this->eventOnSuccess = $value;

        return $this;
    }

    public function setEventOnRequestException(bool $value): self
    {
        $this->eventOnRequestException = $value;

        return $this;
    }

    public function setEventOnConnectionException(bool $value): self
    {
        $this->eventOnConnectionException = $value;

        return $this;
    }

    public function setLogOnRequestException(bool $value): self
    {
        $this->logOnRequestException = $value;

        return $this;
    }

    public function setLogOnConnectionException(bool $value): self
    {
        $this->logOnConnectionException = $value;

        return $this;
    }

}
