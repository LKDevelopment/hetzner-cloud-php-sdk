[![Latest Stable Version](https://poser.pugx.org/lkdevelopment/hetzner-cloud-php-sdk/version)](https://packagist.org/packages/lkdevelopment/hetzner-cloud-php-sdk)
[![License](https://poser.pugx.org/lkdevelopment/hetzner-cloud-php-sdk/license)](https://packagist.org/packages/lkdevelopment/hetzner-cloud-php-sdk)
[![Total Downloads](https://poser.pugx.org/lkdevelopment/hetzner-cloud-php-sdk/downloads)](https://packagist.org/packages/lkdevelopment/hetzner-cloud-php-sdk)
[![Actions Status](https://github.com/lkdevelopment/hetzner-cloud-php-sdk/workflows/CI/badge.svg)](https://github.com/lkdevelopment/hetzner-cloud-php-sdk/actions)
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
### PHP Support

We test on all supported PHP Versions. The library can still work on older versions, however it is no longer actively tested.

### Old Releases: v1.x
[Version 1.x](https://github.com/LKDevelopment/hetzner-cloud-php-sdk/tree/v1) is abandoned and will not receive any new updates or features. V2 was created with Backward Compatibility in mind. So it should work as a drop-in replacement. Therefor it does not give a "Migration to v2"-Guide. It should just work!

### Testing

You can just run `phpunit`. The whole library is based on unit tests and sample responses from the official Hetzner Cloud documentation.

### Changelog

Please see [CHANGELOG](https://github.com/LKDevelopment/hetzner-cloud-php-sdk/releases) for more information what has changed recently.


### Security

If you discover any security related issues, please email kontakt@lukas-kaemmerling.de instead of using the issue tracker.

## Credits

- [Lukas Kämmerling](https://github.com/lkaemmerling)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
