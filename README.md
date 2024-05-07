# Laravel HTTP Facade wrapper

This package provides a way to use Laravel HTTP facade with error handling, logging and events firing.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/stoyantodorov/laravel-api-client.svg?style=flat-square)](https://packagist.org/packages/stoyantodorov/laravel-api-client)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/stoyantodorov/laravel-api-client/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/stoyantodorov/laravel-api-client/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/stoyantodorov/laravel-api-client/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/stoyantodorov/laravel-api-client/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/stoyantodorov/laravel-api-client.svg?style=flat-square)](https://packagist.org/packages/stoyantodorov/laravel-api-client)



## Installation


```bash
composer require stoyantodorov/laravel-api-client
```

## Configuration

You can publish the config file:

```bash
php artisan vendor:publish --tag="laravel-api-client-config"
```

## Usage
Send a request:

```php
use Stoyantodorov\ApiClient\Interfaces\ApiClientInterface;

$apiClient = resolve(ApiClientInterface::class);
$response = $apiClient->get('https://exmple-host', ['queryParam' => 'value']);
```

Add base configurations:

```php
$apiClient = baseConfig(retries: 3, retryInterval: 3000);
```

Use a Http facade methods to add other configurations to PendingRequest:

```php
use Stoyantodorov\ApiClient\Enums\PendingRequestMethod;

$apiClient->configure(PendingRequestMethod::WITH_BASIC_AUTH, [$username, $password]);
```
This code do the same as 
```php
use Illuminate\Support\Facades\Http;

Http::withBasicAuth($username, $password);
```

Use send method for specific HTTP method and format:

```php
use Stoyantodorov\ApiClient\Enums\HttpMethod;
use Stoyantodorov\ApiClient\Enums\HttpRequestFormat;

$apiClient->send(HttpMethod::CONNECT, 'https://exmple-host', HttpRequestFormat::QUERY, []);
```

To send a request without error handling, logging and event firing use:

```php
use Stoyantodorov\ApiClient\Enums\ApiClientRequestMethod;

$apiClient->->sendRequest(ApiClientRequestMethod::GET, 'https://exmple-host');
```

Logging and event firing is configurable trough config values:

```php
    'events' => [
        'onSuccess' => true,
        'onRequestException' => true,
        'onConnectionException' => true,
    ],
    'logs' => [
        'onRequestException' => true,
        'onConnectionException' => true,
    ],
```

```php
use Stoyantodorov\ApiClient\Enums\ApiClientRequestMethod;

$apiClient->->sendRequest(ApiClientRequestMethod::GET, 'https://exmple-host');
```

These configurations can be overridden through ApiClient setters:
```php
$apiClient->->fireEventOnSuccess(false);
```


## API Reference
```php
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
    public function configure(
        PendingRequestMethod $method,
        array $parameters = [],
        bool $newPendingRequest = false
    ): self;

    /**
     * Send a request with given HTTP method, url, options HTTP request format
     * When there is no existing PendingRequest instance, new one is created
     *
     * @param HttpMethod        $httpMethod
     * @param string            $url
     * @param HttpRequestFormat $format
     * @param array             $options
     * @return Response|null
     */
    public function send(
        HttpMethod        $httpMethod,
        string            $url,
        HttpRequestFormat $format,
        array             $options = [],
    ): Response|null;

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
     * @param ApiClientRequestMethod $apiClientMethod
     * @param string                 $url
     * @param array                  $options
     * @param HttpMethod|null        $httpMethod
     * @param bool                   $throw
     * @return Response
     */
    public function getResponse(
        ApiClientRequestMethod $apiClientMethod,
        string $url,
        array $options = [],
        HttpMethod|null $httpMethod = null,
        bool $throw = false,

    ): Response;

    /**
     * Getter for pendingRequest property
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
```

## Enums

```php
enum HttpMethod: string
{
    case HEAD = 'HEAD';
    case GET = 'GET';
    case POST = 'POST';
    case PUT = 'PUT';
    case PATCH = 'PATCH';
    case DELETE = 'DELETE';
    case CONNECT = 'CONNECT';
    case OPTIONS = 'OPTIONS';
    case TRACE = 'TRACE';
}
```

```php
enum HttpRequestFormat: string
{
    case QUERY = 'query';
    case BODY = 'body';
    case JSON = 'json';
    case FORM_PARAMS = 'form_params';
    case MULTIPART = 'multipart';
}
```

```php
    case BASE_URL = 'baseUrl';
    case WITH_BODY = 'withBody';
    case AS_JSON = 'asJson';
    case AS_FORM = 'asForm';
    case ATTACH = 'attach';
    case AS_MULTIPART = 'asMultipart';
    case BODY_FORMAT = 'bodyFormat';
    case WITH_QUERY_PARAMETERS = 'withQueryParameters';
    case CONTENT_TYPE = 'contentType';
    case ACCEPT_JSON = 'acceptJson';
    case ACCEPT = 'accept';
    case WITH_HEADERS = 'withHeaders';
    case WITH_HEADER = 'withHeader';
    case REPLACE_HEADERS = 'replaceHeaders';
    case WITH_BASIC_AUTH = 'withBasicAuth';
    case WITH_DIGEST_AUTH = 'withDigestAuth';
    case WITH_TOKEN = 'withToken';
    case WITH_USER_AGENT = 'withUserAgent';
    case WITH_URL_PARAMETERS = 'withUrlParameters';
    case WITH_COOKIES = 'withCookies';
    case MAX_REDIRECTS = 'maxRedirects';
    case WITHOUT_REDIRECTING = 'withoutRedirecting';
    case WITHOUT_VERIFYING = 'withoutVerifying';
    case SINK = 'sink';
    case TIMOUT = 'timeout';
    case CONNECT_TIMOUT = 'connectTimeout';
    case RETRY = 'retry';
    case WITH_OPTIONS = 'withOptions';
    case WITH_MIDDLEWARE = 'withMiddleware';
    case WITH_REQUEST_MIDDLEWARE = 'withRequestMiddleware';
    case WITH_RESPONSE_MIDDLEWARE = 'withResponseMiddleware';
    case BEFORE_SENDING = 'beforeSending';
    case THROW = 'throw';
    case THROW_IF = 'throwIf';
    case DUMP = 'dump';
    case DD = 'dd';
```

```php
enum ApiClientRequestMethod: string
{
    case GET = 'get';
    case HEAD = 'head';
    case POST = 'post';
    case PATCH = 'patch';
    case PUT = 'put';
    case DELETE = 'delete';
    case SEND = 'send';
}
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Stoyan Todorov](https://github.com/StoyanTodorov)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
