<?php

namespace Stoyantodorov\ApiClient\Interfaces;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Stoyantodorov\ApiClient\Enums\HttpRequestFormat;
use Stoyantodorov\ApiClient\Enums\HttpMethod;
use Stoyantodorov\ApiClient\Enums\ApiClientRequestMethod;

interface ApiClientInterface
{
    /**
     * Base configuration for PendingRequest
     * When PendingRequest instance isn't received, new one is created
     * Resets pendingRequest property when receives a PendingRequest instance
     *
     * @param array               $headers
     * @param int                 $retries
     * @param int                 $retryInterval
     * @param int                 $timeout
     * @param int                 $connectTimeout
     * @param string|null         $userAgent
     * @param PendingRequest|null $pendingRequest
     * @return self
     */
    public function baseConfig(
        array               $headers = [],
        int                 $retries = 1,
        int                 $retryInterval = 1000,
        int                 $timeout = 30,
        int                 $connectTimeout = 3,
        string|null         $userAgent = null,
        PendingRequest|null $pendingRequest = null,
    ): self;

    /**
     * Send a request with given HTTP method, url, options HTTP request format
     * When PendingRequest instance isn't received, new one is created
     * Resets pendingRequest property when receives a PendingRequest instance
     *
     * @param HttpMethod          $httpMethod
     * @param string              $url
     * @param HttpRequestFormat   $format
     * @param array               $options
     * @param PendingRequest|null $pendingRequest
     * @return Response|null
     */
    public function send(
        HttpMethod          $httpMethod,
        string              $url,
        HttpRequestFormat   $format,
        array               $options = [],
        PendingRequest|null $pendingRequest = null
    ): Response|null;

    /**
     * Send HEAD request
     * When PendingRequest instance isn't received, new one is created
     * Resets pendingRequest property when receives a PendingRequest instance
     *
     * @param string              $url
     * @param array               $parameters
     * @param PendingRequest|null $pendingRequest
     * @return Response|null
     */
    public function head(string $url, array $parameters = [], PendingRequest|null $pendingRequest = null): Response|null;

    /**
     * Send GET request
     * When PendingRequest instance isn't received, new one is created
     * Resets pendingRequest property when receives a PendingRequest instance
     *
     * @param string              $url
     * @param array               $parameters
     * @param PendingRequest|null $pendingRequest
     * @return Response|null
     */
    public function get(string $url, array $parameters = [], PendingRequest|null $pendingRequest = null): Response|null;

    /**
     * Send POST request
     * When PendingRequest instance isn't received, new one is created
     *
     * @param string              $url
     * @param array               $body
     * @param PendingRequest|null $pendingRequest
     * @return Response|null
     */
    public function post(string $url, array $body = [], PendingRequest|null $pendingRequest = null): Response|null;

    /**
     * Send PUT request
     * When PendingRequest instance isn't received, new one is created
     * Resets pendingRequest property when receives a PendingRequest instance
     *
     * @param string              $url
     * @param array               $body
     * @param PendingRequest|null $pendingRequest
     * @return Response|null
     */
    public function put(string $url, array $body = [], PendingRequest|null $pendingRequest = null): Response|null;

    /**
     * Send PATCH request
     * When PendingRequest instance isn't received, new one is created
     * Resets pendingRequest property when receives a PendingRequest instance
     *
     * @param string              $url
     * @param array               $body
     * @param PendingRequest|null $pendingRequest
     * @return Response|null
     */
    public function patch(string $url, array $body = [], PendingRequest|null $pendingRequest = null): Response|null;

    /**
     * Send DELETE request
     * When PendingRequest instance isn't received, new one is created
     * Resets pendingRequest property when receives a PendingRequest instance
     *
     * @param string              $url
     * @param array               $body
     * @param PendingRequest|null $pendingRequest
     * @return Response|null
     */
    public function delete(string $url, array $body = [], PendingRequest|null $pendingRequest = null): Response|null;

    /**
     * Send a request by given ApiClient request method, url, options
     * Catches RequestException and ConnectionException
     * Logs messages
     * Fires events depending on the configurations
     * $httpMethod parameter should be provided when $apiClientRequestMethod is ApiClientRequestMethod::SEND
     * When PendingRequest instance isn't received, new one is created
     *
     * @param ApiClientRequestMethod $apiClientRequestMethod
     * @param string                 $url
     * @param array                  $options
     * @param PendingRequest|null    $pendingRequest
     * @param HttpMethod|null        $httpMethod
     * @return Response|null
     */
    public function sendRequest(
        ApiClientRequestMethod $apiClientRequestMethod,
        string                 $url,
        array                  $options = [],
        PendingRequest|null    $pendingRequest = null,
        HttpMethod|null        $httpMethod = null,
    ): Response|null;

    /**
     * Send request without error handling and event triggering
     * Throw RequestException when throw is true
     * Resets pendingRequest property when receives a PendingRequest instance
     *
     * @param ApiClientRequestMethod $apiClientMethod
     * @param string                 $url
     * @param array                  $options
     * @param HttpMethod|null        $httpMethod
     * @param bool                   $throw
     * @return Response
     */
    public function request(
        ApiClientRequestMethod $apiClientMethod,
        string                 $url,
        array                  $options = [],
        HttpMethod|null        $httpMethod = null,
        bool                   $throw = false,

    ): Response;

    /**
     * Set PendingRequest when not null value is received
     *
     * @param PendingRequest|null $pendingRequest
     * @return self
     */
    public function setPendingRequest(PendingRequest|null $pendingRequest): self;

    /**
     * Get pendingRequest
     *
     * @return PendingRequest|null
     */
    public function getPendingRequest(): PendingRequest|null;

    /**
     * Determine if HttpResponseSucceeded event is fired
     *
     * @param bool $value
     * @return self
     */
    public function fireEventOnSuccess(bool $value): self;

    /**
     * Determine if HttpRequestFailed event is fired
     *
     * @param bool $value
     * @return self
     */
    public function fireEventOnRequestException(bool $value): self;

    /**
     * Determine if HttpConnectionFailed event is fired
     *
     * @param bool $value
     * @return self
     */
    public function fireEventOnConnectionException(bool $value): self;

    /**
     * Determine if RequestException occurring is logged
     *
     * @param bool $value
     * @return self
     */
    public function logOnRequestException(bool $value): self;

    /**
     * Determine if ConnectionException occurring is logged
     *
     * @param bool $value
     * @return self
     */
    public function logOnConnectionException(bool $value): self;
}
