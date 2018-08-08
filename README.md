[![Latest Stable Version](https://poser.pugx.org/lkdevelopment/hetzner-cloud-php-sdk/version)](https://packagist.org/packages/lkdevelopment/hetzner-cloud-php-sdk)
[![License](https://poser.pugx.org/lkdevelopment/hetzner-cloud-php-sdk/license)](https://packagist.org/packages/lkdevelopment/hetzner-cloud-php-sdk)
[![Total Downloads](https://poser.pugx.org/lkdevelopment/hetzner-cloud-php-sdk/downloads)](https://packagist.org/packages/lkdevelopment/hetzner-cloud-php-sdk)
[![Build Status](https://travis-ci.com/LKDevelopment/hetzner-cloud-php-sdk.svg?branch=master)](https://travis-ci.com/LKDevelopment/hetzner-cloud-php-sdk)
# Hetzner Cloud PHP SDK
A PHP SDK for the Hetzner Cloud API: https://docs.hetzner.cloud/
## Installation

You can install the package via composer:

```bash
composer require lkdevelopment/hetzner-cloud-php-sdk
```

## Usage

``` php
$hetznerClient = new \LKDev\HetznerCloud\HetznerAPIClient($apiKey);
foreach ($hetznerClient->servers()->all() as $server) {
    echo 'ID: '.$server->id.' Name:'.$server->name.' Status: '.$server->status.PHP_EOL;
}
```

### Testing

We use the [Hetzner Cloud API Mock Server](https://github.com/LKDevelopment/hetzner-cloud-api-mock) for testing against the API. For testing run the commands:
```bash
docker run -d -p 127.0.0.1:4000:8080 lkdevelopment/hetzner-cloud-api-mock
phpunit
```

### Changelog

Please see [CHANGELOG](https://github.com/LKDevelopment/hetzner-cloud-php-sdk/releases) for more information what has changed recently.


### Security

If you discover any security related issues, please email kontakt@lukas-kaemmerling.de instead of using the issue tracker.

## Credits

- [Lukas Kämmerling](https://github.com/lkdevelopment)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
