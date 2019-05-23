# Modulo de Pago Imagina Magento 2 para  [Placetopay](https://www.placetopay.com)

*Otros idiomas del Readme: [Español](README.es.md),[English](README.md)

Integración [Placetopay](https://www.placetopay.com) para Magento 2

## Características
- Reedirection
- Soporte a multi-tienda,
- Registro de acciones, errores, etc
- Modo de Prueba Testing
- Disponible solo para Colombia (Por el momento)

## Configuración en Panel de Magento 2

La configuración puede encontrarse en "Stores > Configuration > Sales > Payment Methods > Imagina Placetopay."

    - Mode = "Development" para desarrollo o pruebas
    - Login = Proporcionado por la plataforma de PlaceToPay
    - Secret Key = Proporcionado por la plataforma de PlaceToPay

## Como instalar
Desde la linea de comandos en la raiz de magento:
```ssh
composer require imagina/magento2-placetopay
php bin/magento setup:upgrade
php bin/magento setup:di:compile
```
## Vendor

Agregar en el composer.json "dnetix/redirection": "dev-master" (En el Magento Root)

"require": {
    "dnetix/redirection": "dev-master",  
},

Ejecutar: composer update

Mas información:  https://www.imaginacolombia.com