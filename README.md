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
$servers = new \LKDev\HetznerCloud\Models\Servers\Servers();
foreach ($servers->all() as $server) {
    echo 'ID: '.$server->id.' Name:'.$server->name.' Status: '.$server->status.PHP_EOL;
}
```

### Testing

``` bash
phpunit
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email kontakt@lukas-kaemmerling.de instead of using the issue tracker.

## Credits

- [Lukas Kämmerling](https://github.com/lkdevelopment)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
