# Spin Payment plugin for Magento 2
Use Spin's plugin for Magento 2 to offer frictionless payments online in-store.

## Requirements
This plugin supports Magento 2 version 2.4.4 and higher.

## Releases

1. **Major** releases are done ONLY when absolutely required. We try to not to introduce breaking changes and do major releases as rare as possible. Current average is **yearly**.

**Note: This can be subject to change based on the load and dependancies of the team.**

## Contributing
We strongly encourage you to join us in contributing to this repository so everyone can benefit from:
* New features and functionality
* Resolved bug fixes and issues
* Any general improvements

Read our [**contribution guidelines**](CONTRIBUTING.md) to find out how.


## Installation
You can install our plugin through Composer:
```
composer config repositories.digitalfemsa git https://github.com/digitalfemsa/customer-magento-plugin.git
composer require digitalfemsa/digitalfemsa_payments
bin/magento module:enable DigitalFemsa_Payments
bin/magento setup:upgrade
```
For more information see our [installation section](https://developers.digitalfemsa.io/docs/magento-230).

## Documentation
- [Magento 2 documentation](https://developers.digitalfemsa.io/docs/magento-230)

## Supported payment methods

See our [documentation](https://developers.digitalfemsa.io/docs/paso-3-configura-el-plugin-para-magento-23x) for a full list of supported payment methods.

## Support
If you have a feature request, or spotted a bug or a technical problem, create a GitHub issue. For other questions, contact our [support team](https://developers.digitalfemsa.io/discuss).

## API Library
This module is using the Digitalfemsa APIs Library for PHP for all (API) connections to Digitalfemsa.
<a href="https://github.com/digitalfemsa/femsa-php" target="_blank">This library can be found here</a>

## License
MIT license. For more information, see the [LICENSE](LICENSE.txt) file.
