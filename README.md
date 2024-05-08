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

Resolve an HttpClient instance in a way that fits what you need, for example:

```php
use Stoyantodorov\ApiClient\Interfaces\HttpClientInterface;

$apiClient = resolve(HttpClientInterface::class);
```

Send a request:
```php
$response = $apiClient->get('https://exmple-host', ['queryParam' => 'value']);
```

You can configure PendingRequest in advance:
```php
use Illuminate\Support\Facades\Http;

$apiClient->get('https://exmple-host', ['queryParam' => 'value'], Http::withToken($token));
```

There is a method to add base configurations:
```php
$apiClient = baseConfig(retries: 3, retryInterval: 3000, timout: 60, connectTimeout: 5, userAgent: 'Test');
```

It also can receive a configured PendingRequest:
```php
use Illuminate\Support\Facades\Http;

$apiClient = baseConfig(retries: 3, pendingRequest: Http::withToken($token));
```

Use send method for specific HTTP method and format:

```php
use Stoyantodorov\ApiClient\Enums\HttpMethod;
use Stoyantodorov\ApiClient\Enums\HttpRequestFormat;

$apiClient->send(HttpMethod::CONNECT, 'https://exmple-host', HttpRequestFormat::QUERY, []);
```

Optionally you can add configured PendingRequest to it too:

```php
use Illuminate\Support\Facades\Http;
use Stoyantodorov\ApiClient\Enums\HttpMethod;
use Stoyantodorov\ApiClient\Enums\HttpRequestFormat;

$apiClient->send(HttpMethod::CONNECT, 'https://exmple-host', HttpRequestFormat::QUERY, [], Http::withToken($token));
```

When you need to send a request without error handling, logging and event firing may use:

```php
use Stoyantodorov\ApiClient\Enums\HttpClientRequestMethod;

$apiClient->->sendRequest(HttpClientRequestMethod::GET, 'https://exmple-host');
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

These configurations can be overridden through HttpClient setters:
```php
$apiClient->fireEventOnSuccess(false);
```


## API Reference
### HttpClientInterface
```php
    /**
     * Base configuration for PendingRequest
     * When PendingRequest instance isn't received, new one is created
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
     *
     * @param string              $url
     * @param array               $body
     * @param PendingRequest|null $pendingRequest
     * @return Response|null
     */
    public function delete(string $url, array $body = [], PendingRequest|null $pendingRequest = null): Response|null;

    /**
     * Send a request by given HttpClient request method, url, options
     * Catches RequestException and ConnectionException
     * Logs messages
     * Fires events depending on the configurations
     * $httpMethod parameter should be provided when $apiClientRequestMethod is HttpClientRequestMethod::SEND
     * When PendingRequest instance isn't received, new one is created
     *
     * @param HttpClientRequestMethod $apiClientRequestMethod
     * @param string                 $url
     * @param array                  $options
     * @param PendingRequest|null    $pendingRequest
     * @param HttpMethod|null        $httpMethod
     * @return Response|null
     */
    public function sendRequest(
        HttpClientRequestMethod $apiClientRequestMethod,
        string                 $url,
        array                  $options = [],
        PendingRequest|null    $pendingRequest = null,
        HttpMethod|null        $httpMethod = null,
    ): Response|null;

    /**
     * Send request without error handling and event triggering
     * Throw RequestException when throw is true
     *
     * @param HttpClientRequestMethod $apiClientMethod
     * @param string                 $url
     * @param array                  $options
     * @param HttpMethod|null        $httpMethod
     * @param bool                   $throw
     * @return Response
     */
    public function request(
        HttpClientRequestMethod $apiClientMethod,
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
enum HttpClientRequestMethod: string
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
