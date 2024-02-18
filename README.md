# Payment Processor test task

## Installation

Open root of project via terminal and specify:
```shell
composer install
```

Minimum requirements is a PHP 8.1 and installed library BCMath

## Run app

Basically run in the terminal from the project root command:
```shell
./fees {path_to_your_file}
```
Project contains a CSV example of the such file, so you could use it:
```shell
./fees test.csv
```


The core code of the solution is framework-agnostic, so three application examples based on different platforms have been added to the project: Laravel, Symfony, and a custom build composed of various components.

You can switch application
```shell
./fees {path_to_your_file} {application_driver}
```

 - `custom` - for custom application
 - `laravel` - for Laravel-based application
 - `symfony` - for Symfony-based application

Default application is `custom`

Example:
```shell
./fees test.csv laravel
```
```shell
./fees test.csv symfony
```
```shell
./fees test.csv custom
```

## Testing

```shell
composer run test
```

## Documentation
 1. [General](docs/general_info.md)
 2. [Code guidance](docs/code)
    1. [Entities](docs/code/entites.md)
    2. [Valuation](docs/code/valuation.md)
    3. [Valuation Service](docs/code/valuation_service.md)
    4. [Currency Fee Valuation Toolkit](docs/code/commissions_fee_valuation.md)