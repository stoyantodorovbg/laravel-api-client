# Laravel HTTP Facade wrapper and Token service

This package provides a way to use Laravel HTTP facade with error handling, logging and events firing. There is also Token Service that makes requests to retrieve access token depending on stored configurations or received parameters.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/stoyantodorov/laravel-api-client.svg?style=flat-square)](https://packagist.org/packages/stoyantodorov/laravel-api-client)
[![Total Downloads](https://img.shields.io/packagist/dt/stoyantodorov/laravel-api-client.svg?style=flat-square)](https://packagist.org/packages/stoyantodorov/laravel-api-client)



## Installation

```bash
composer require stoyantodorov/laravel-api-client
```

## Usage

### HttpClient Service

Resolve an **HttpClient** instance in a way that fits what you need, for example:

```php
use Stoyantodorov\ApiClient\Interfaces\HttpClientInterface;

$apiClient = resolve(HttpClientInterface::class);
```

Send a request:
```php
$response = $apiClient->get('https://exmple-host', ['queryParam' => 'value']);
```

You can configure **PendingRequest** in advance:
```php
use Illuminate\Support\Facades\Http;

$apiClient->get('https://exmple-host', ['queryParam' => 'value'], Http::withToken($token));
```

You can also use **baseConfig** method to add base configurations:
```php
$apiClien->baseConfig(retries: 3, retryInterval: 3000, timout: 60, connectTimeout: 5, userAgent: 'Test');
```

This method can receive a configured PendingRequest:
```php
use Illuminate\Support\Facades\Http;

$apiClient->baseConfig(retries: 3, pendingRequest: Http::withToken($token));
```

Use **send** method for specific HTTP method and format:

```php
use Stoyantodorov\ApiClient\Enums\HttpMethod;
use Stoyantodorov\ApiClient\Enums\HttpRequestFormat;

$apiClient->send(HttpMethod::CONNECT, 'https://exmple-host', HttpRequestFormat::QUERY, []);
```

Optionally you can add configured **PendingRequest** to it too:

```php
use Illuminate\Support\Facades\Http;
use Stoyantodorov\ApiClient\Enums\HttpMethod;
use Stoyantodorov\ApiClient\Enums\HttpRequestFormat;

$apiClient->send(HttpMethod::CONNECT, 'https://exmple-host', HttpRequestFormat::QUERY, [], Http::withToken($token));
```

When you need to send a request without error handling, logging and event firing may use **sendRequest**:

```php
use Stoyantodorov\ApiClient\Enums\HttpClientRequestMethod;

$apiClient->->sendRequest(HttpClientRequestMethod::GET, 'https://exmple-host');
```

Logging and event firing is configurable trough **config** values:

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

These configurations can be overridden through **HttpClient** setters:
```php
$apiClient->fireEventOnSuccess(false);
```

### Token Service

#### Factories

There are two factories which instantiate **TokenService**. The first one sets configurations from **config** file:

```php
use Stoyantodorov\ApiClient\Factories\TokenFromConfigFactory;

TokenFromConfigFactory::create();
```
When you are using this factory should set the relevant configurations in **api-client.php**:
```php
'tokenConfigurationsBase' => [
        'accessTokenRequest' => [
            'url' => '',
            'body' => [],
            'headers' => [],
            'responseNestedKeys' => ['access_token'],
            'method' => 'post',
            'dispatchEvent' => true,
        ],
        'refreshTokenRequest' => [
            'url' => '',
            'body' => [],
            'headers' => [],
            'responseNestedKeys' => ['access_token'],
            'method' => 'post',
            'dispatchEvent' => true,
        ],
        'tokenRequestsRetries' => 3,
    ],
```

The second factory sets configurations from data objects:

```php
use Stoyantodorov\ApiClient\Factories\TokenFromDataFactory;
use Stoyantodorov\ApiClient\Data\TokenData;

$tokenData new TokenData(
            url: 'https://example-host/access-token',
            body: ['username' => 'testValue', 'password' => 'testValue'],
        );

TokenFromDataFactory::create($tokenData);
```

When you are using an endpoint to refresh a token, the factories should be instructed to send such configurations:

```php
TokenFromConfigFactory::create(hasRefreshTokenRequest: true);

use Stoyantodorov\ApiClient\Data\RefreshTokenData;
```

```php
$refreshTokenData = new RefreshTokenData(
            url: 'https://example-host/refresh-token',
            body: ['refreshToken' => 'testValue'],
        );
TokenFromDataFactory::create(tokenData: $tokenData, refreshTokenData: $refreshTokenData);
```

You may instruct **TokenFromConfigFactory** to loads configurations from other config key:
```php
TokenFromConfigFactory::create(configKey: 'anotherApiConfigurations');
```

If you have an already received token, can send it to both factories as optional parameter:
```php
TokenFromConfigFactory::>create(token: 'someValueOrNull');
TokenFromDataFactory::create(token: 'someValueOrNull');
```

#### Obtain a token

**get** method sends a token request:
```php
$service->get();
```

If the service is instantiated with an already obtained token, the method returns it instead of sending a new request.

The same method can also send a request that refreshes the token:
```php
$service->get(refresh: true);
```

The JSON path to the token in the response should be set in **config** file:
```php
'responseNestedKeys' => ['data', 'access_token'],
```
or in **TokenData** and **RefreshTokenData**
```php
new TokenData(
            url: 'https://example-host/access-token',
            body: ['username' => 'testValue', 'password' => 'testValue'],
            responseNestedKeys: ['data', 'access_token'],
        );
new RefreshTokenData(
            url: 'https://example-host/refresh-token',
            body: ['refreshToken' => 'testValue'],
            responseNestedKeys: ['data', 'access_token'],
        );
```

When you need to access the response can subscribe for the events which are fired by default:
```php
use Stoyantodorov\ApiClient\Events\AccessTokenObtained;

public function handle(AccessTokenObtained $event): void
{
    $response = $vent->response;
}
```
```php
use Stoyantodorov\ApiClient\Events\AccessTokenRefreshed;

public function handle(AccessTokenRefreshed $event): void
{
    $response = $vent->response;
}
```

If you aren't using these events may switch them off from the **config** file:
```php
'dispatchEvent' => false,
```
or **TokenData** and **RefreshTokenData**:
```php
new TokenData(
            url: 'https://example-host/access-token',
            body: ['username' => 'testValue', 'password' => 'testValue'],
            dispatchEvent: false,
        );
new RefreshTokenData(
            url: 'https://example-host/refresh-token',
            body: ['refreshToken' => 'testValue'],
            dispatchEvent: false,
        );
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

### Enums

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

### TokenFromConfigFactoryInterface

```php
    /**
     * Instantiate TokenInterface
     * When receives $token it is set in TokenInterface instance
     * $hasRefreshTokenRequest determines instantiating RefreshTokenData
     * $configKey refers to token configurations in the config file
     *
     * @param bool        $hasRefreshTokenRequest
     * @param string      $configKey
     * @param string|null $token = null
     * @return TokenInterface
     */
    public static function create(
                              bool $hasRefreshTokenRequest = true,
                              string $configKey = 'tokenConfigurationsBase',
                              #[SensitiveParameter] string|null $token = null,
    ): TokenInterface;
```

### TokenFromDataFactoryInterface

```php
    /**
     * Instantiate TokenInterface
     * When receives $token it is set in TokenInterface instance
     *
     * @param TokenData             $tokenData
     * @param RefreshTokenData|null $refreshTokenData = null
     * @param int                   $retries = 3
     * @param string|null           $token = null
     * @return TokenInterface
     */
    public static function create(
        #[SensitiveParameter] TokenData             $tokenData,
        #[SensitiveParameter] RefreshTokenData|null $refreshTokenData = null,
                              int                   $retries = 3,
        string|null                                  $token = null,
    ): TokenInterface;
```

### TokenInterface

```php
    /**
     * Get Access Token
     * When it's missing request it
     * When $refresh is true make a request to refresh the token
     *
     * @param bool $refresh = false
     * @return string
     */
    public function get(bool $refresh = false): string;
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
