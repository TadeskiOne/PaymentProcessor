# Payment Processor test task

## Installation

You can up Docker container:
```shell
docker-compose up -d
```
or do it directly

Open root of project via terminal and specify:
```shell
composer install
```
OR via Docker
```shell
docker exec pay-process composer install
```

The minimum requirement is a PHP 8.1 if you run an app without Docker

Then, define env-variable CURRENCY_API_PATH in `.env.example`
```
CURRENCY_API_PATH='{path_to_get_rates_from_currency_api}'
```
And run:
```shell
composer run set-envs
```
OR via Docker
```shell
 docker exec pay-process composer run set-envs
```

**If `CURRENCY_API_PATH` is not specified, will be used default (you can actually skip step with setting this var and just set up envs)**

## Run app

Run in the terminal from the project root command:
```shell
./fees {path_to_your_file}
```
The project contains a CSV example of the such file, so you could use it:
```shell
./fees test.csv
```

The same via Docker:
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

The default application is `custom`

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
Via Docker:
```shell
 docker exec pay-process composer run test
```

## Documentation
 1. [The Task](docs/task.md)
 2. [General](docs/general_info.md)
 3. [Code guidance](docs/code)
    1. [Entities](docs/code/entites.md)
    2. [Valuation](docs/code/valuation.md)
    3. [Valuation Service](docs/code/valuation_service.md)
    4. [Currency Fee Valuation Toolkit](docs/code/commissions_fee_valuation.md)
