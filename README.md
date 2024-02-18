# Payment Processor test task

## Installation

Open root of project via terminal and specify:
```shell
composer install
```

Minimum requirements is a PHP 8.1 or up Docker container
```shell
docker-compose up -d
```

Then, define env-variable CURRENCY_API_PATH in `.env.example`
```
CURRENCY_API_PATH='{path_to_get_rates_from_currency_api}'
```
And run:
```shell
composer run set-envs
```

## Run app

Basically run in the terminal from the project root command:
```shell
./fees {path_to_your_file}
```
Project contains a CSV example of the such file, so you could use it:
```shell
./fees test.csv
```

The same via Docker:
```shell
docker exec -it pay-process bash
>> ./fees test.csv
```
OR
```shell
docker exec pay-process ./fees test.csv
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
And via Docker:
```shell
docker exec pay-process ./fees test.csv laravel
```
```shell
docker exec pay-process ./fees test.csv symfony
```
```shell
docker exec pay-process ./fees test.csv custom
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