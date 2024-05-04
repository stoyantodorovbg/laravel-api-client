# Laravel API client

[![Latest Version on Packagist](https://img.shields.io/packagist/v/stoyantodorov/laravel-api-client.svg?style=flat-square)](https://packagist.org/packages/stoyantodorov/laravel-api-client)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/stoyantodorov/laravel-api-client/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/stoyantodorov/laravel-api-client/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/stoyantodorov/laravel-api-client/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/stoyantodorov/laravel-api-client/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/stoyantodorov/laravel-api-client.svg?style=flat-square)](https://packagist.org/packages/stoyantodorov/laravel-api-client)

This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.

## Installation

You can install the package via composer:

```bash
composer require stoyantodorov/laravel-api-client
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="laravel-api-client-config"
```

This is the contents of the published config file:

```php
return [
];
```

## Usage

```php
$apiClient = new Stoyantodorov\ApiClient();
echo $apiClient->echoPhrase('Hello, Stoyantodorov!');
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
