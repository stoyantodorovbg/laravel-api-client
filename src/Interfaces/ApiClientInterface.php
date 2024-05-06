<?php

namespace Stoyantodorov\ApiClient\Interfaces;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Stoyantodorov\ApiClient\Enums\HttpRequestFormat;
use Stoyantodorov\ApiClient\Enums\HttpMethod;
use Stoyantodorov\ApiClient\Enums\PendingRequestMethod;
use Stoyantodorov\ApiClient\Enums\ApiClientRequestMethod;

interface ApiClientInterface
{
    /**
     * Base configuration for PendingRequest
     * New PendingRequest instance is created unless $newPendingRequest parameters is false
     *
     * @param array       $headers
     * @param int         $retries
     * @param int         $retryInterval
     * @param int         $timeout
     * @param int         $connectTimeout
     * @param string|null $userAgent
     * @param bool        $newPendingRequest
     * @return self
     */
    public function baseConfig(
        array $headers = [],
        int $retries = 1,
        int $retryInterval = 1000,
        int $timeout = 30,
        int $connectTimeout = 3,
        string|null $userAgent = null,
        bool $newPendingRequest = true,
    ): self;

    /**
     * Add a method to PendingRequest
     * The method is added to the existing PendingRequest instance
     * New PendingRequest instance is created when there is no existing one or $newPendingRequest is true
     *
     * @param PendingRequestMethod $method
     * @param array                $parameters
     * @param bool                 $newPendingRequest
     * @return self
     */
    public function addPendingRequestMethod(
        PendingRequestMethod $method,
        array $parameters = [],
        bool $newPendingRequest = false
    ): self;

    /**
     * Add withBasicAuth method to PendingRequest instance
     * When PendingRequest instance doesn't exist, new one is created
     *
     * @param string $username
     * @param string $password
     * @return self
     */
    public function withBasicAuth(string $username, string $password): self;

    /**
     * Add withDigestAuth method to PendingRequest instance
     * When there is no existing PendingRequest instance, new one is created
     *
     * @param string $username
     * @param string $password
     * @return self
     */
    public function withDigestAuth(string $username, string $password): self;

    /**
     * Send a request with given HTTP method, url, options HTTP request format
     * When there is no existing PendingRequest instance, new one is created
     *
     * @param HttpMethod             $httpMethod
     * @param string                 $url
     * @param array                  $options
     * @param HttpRequestFormat|null $format
     * @return Response
     */
    public function send(
        HttpMethod             $httpMethod,
        string                 $url,
        array                  $options = [],
        HttpRequestFormat|null $format = null
    ): Response;

    /**
     * Send HEAD request
     * When there is no existing PendingRequest instance, new one is created
     *
     * @param string $url
     * @param array  $parameters
     * @return Response|null
     */
    public function head(string $url, array $parameters = []): Response|null;

    /**
     * Send GET request
     * When there is no existing PendingRequest instance, new one is created
     *
     * @param string $url
     * @param array  $parameters
     * @return Response|null
     */
    public function get(string $url, array $parameters = [],): Response|null;

    /**
     * Send POST request
     * When there is no existing PendingRequest instance, new one is created
     *
     * @param string $url
     * @param array  $body
     * @return Response|null
     */
    public function post(string $url, array $body = []): Response|null;

    /**
     * Send PUT request
     * When there is no existing PendingRequest instance, new one is created
     *
     * @param string $url
     * @param array  $body
     * @return Response|null
     */
    public function put(string $url, array $body = []): Response|null;

    /**
     * Send PATCH request
     * When there is no existing PendingRequest instance, new one is created
     *
     * @param string $url
     * @param array  $body
     * @return Response|null
     */
    public function patch(string $url, array $body = []): Response|null;

    /**
     * Send DELETE request
     * When there is no existing PendingRequest instance, new one is created
     *
     * @param string $url
     * @param array  $body
     * @return Response|null
     */
    public function delete(string $url, array $body = []): Response|null;

    /**
     * Send a request by given ApiClient request method, url, options
     * $httpMethod parameter should be provided with send method
     *
     * @param ApiClientRequestMethod $apiClientRequestMethod
     * @param string                 $url
     * @param array                  $options
     * @param HttpMethod|null        $httpMethod
     * @return Response|null
     */
    public function sendRequest(
        ApiClientRequestMethod $apiClientRequestMethod,
        string                 $url,
        array                  $options = [],
        HttpMethod|null        $httpMethod = null,
    ): Response|null;

    /**
     * Get a response without error handling and event triggering
     * Throw RequestException when throw is true
     *
     * @param ApiClientRequestMethod $requestMethod
     * @param string                 $url
     * @param array                  $options
     * @param bool                   $throw
     * @return Response
     */
    public function getResponse(
        ApiClientRequestMethod $requestMethod,
        string $url,
        array $options = [],
        bool $throw = false,
    ): Response;

    /**
     * Getter for pendingRequest property
     *
     * @return PendingRequest|null
     */
    public function getPendingRequest(): PendingRequest|null;
}
