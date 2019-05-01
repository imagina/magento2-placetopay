# Imagina Magento 2 PlacetpLatam Module

*Read this in other languages: [Español](README.es.md),[English](README.md)

PlacetpLatam integration for Magento 2

## Key features
- Webcheckout
- multi-store support,
- Initial integration with Magento payment flow (transactions, refunds, etc.),
- logging all APIs exceptions and errors,
- test mode

## Configuration in PlacetpLatam panel

"Return address" should be set to "yourdomain/placetopay/payment/end"

"Report/Confirmation Address" should be set to "yourdomain/placetopay/payment/notify"


## Configuration in Magento panel

The configuration can be found in Stores > Configuration > Sales > Payment Methods > Imagina PlacetpLatam. It should be pretty straight-forward.

## How to Install
From the command line in magento root:
```ssh
composer require imagina/magento2-placetopay
php bin/magento setup:upgrade
php bin/magento setup:di:compile
```

More Information:  https://www.imaginacolombia.com