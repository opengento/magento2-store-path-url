# Store Path Url Module for Magento 2

[![Latest Stable Version](https://img.shields.io/packagist/v/opengento/module-store-path-url.svg?style=flat-square)](https://packagist.org/packages/opengento/module-store-path-url)
[![License: MIT](https://img.shields.io/github/license/opengento/magento2-store-path-url.svg?style=flat-square)](./LICENSE) 
[![Packagist](https://img.shields.io/packagist/dt/opengento/module-store-path-url.svg?style=flat-square)](https://packagist.org/packages/opengento/module-store-path-url/stats)
[![Packagist](https://img.shields.io/packagist/dm/opengento/module-store-path-url.svg?style=flat-square)](https://packagist.org/packages/opengento/module-store-path-url/stats)

This module allows to override the store code in url with another path value.

 - [Setup](#setup)
   - [Composer installation](#composer-installation)
   - [Setup the module](#setup-the-module)
 - [Features](#features)
 - [Settings](#settings)
 - [Documentation](#documentation)
 - [Support](#support)
 - [Authors](#authors)
 - [License](#license)

## Setup

Magento 2 Open Source or Commerce edition is required.

### Composer installation

Run the following composer command:

```
composer require opengento/module-store-path-url
```

### Setup the module

Run the following magento command:

```
bin/magento setup:upgrade
```

**If you are in production mode, do not forget to recompile and redeploy the static resources.**

## Features

The module allows to replace the store code in url with the following options:  

- Country Code, e.g: be
- Language Code, e.g: fr
- Locale Code (separated with a hyphen), e.g: fr-be
- Locale Code (separated with an underscore), e.g: fr_be
- Custom (you can setup the path of your choice), e.g: emea

Besides that, this module makes the usage of the MAGE_RUN_TYPE and MAGE_RUN_CODE variables optionals. Magento is going to be able to resolve the correct store based on its base web URL.

## Settings

The configuration for this module is available in `Stores > Configuration > Web > Url`.  

## Documentation

In order to use this module, you must enable the following setting: "Add Store Code to Urls" (`web/url/use_store`).  
A new field is added in the configuration: "Custom Store Path Url" and "Custom Path Mapper" fi the "custom" value is selected.  
The store path config are:  

- `web/url/use_store_path`
- `web/url/custom_path_mapper`

## Support

Raise a new [request](https://github.com/opengento/magento2-store-path-url/issues) to the issue tracker.

## Authors

- **Opengento Community** - *Lead* - [![Twitter Follow](https://img.shields.io/twitter/follow/opengento.svg?style=social)](https://twitter.com/opengento)
- **Thomas Klein** - *Maintainer* - [![GitHub followers](https://img.shields.io/github/followers/thomas-kl1.svg?style=social)](https://github.com/thomas-kl1)
- **Contributors** - *Contributor* - [![GitHub contributors](https://img.shields.io/github/contributors/opengento/magento2-store-path-url.svg?style=flat-square)](https://github.com/opengento/magento2-store-path-url/graphs/contributors)

## License

This project is licensed under the MIT License - see the [LICENSE](./LICENSE) details.

***That's all folks!***
