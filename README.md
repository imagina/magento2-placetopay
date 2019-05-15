# Imagina Magento 2 PlacetpLatam Module

*Read this in other languages: [Español](README.es.md),[English](README.md)

PlacetpLatam integration for Magento 2

## Key features
- Reedirection
- multi-store support,
- logging all APIs exceptions and errors,
- test mode
- Available just for Colombia (For now)

## Configuration in Magento panel

The configuration can be found in Stores > Configuration > Sales > Payment Methods > Imagina PlacetpLatam. It should be pretty straight-forward.

    - Mode = "Development" for devs
    - Login = Provided by PlaceToPay plataform
    - Secret Key = Provided by PlaceToPay plataform

## How to Install
From the command line in magento root:
```ssh
composer require imagina/magento2-placetopay
php bin/magento setup:upgrade
php bin/magento setup:di:compile
```

## Vendor

Add to composer.json "dnetix/redirection": "dev-master" (Magento Root)

"require": {
    "dnetix/redirection": "dev-master",  
},

Execute: composer update

More Information:  https://www.imaginacolombia.com