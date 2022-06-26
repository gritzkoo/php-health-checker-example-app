# PHP Health Checker example test project

## Instructions

1. Clone this repository

    ```sh
    git clone https://github.com/gritzkoo/php-health-checker-example-app
    ```

2. Available commands

```sh
make start # will bootstrap the project, needs docker docker-compose
make up # will start laravel and open browser to navigate in livenss and readiness actions
make down # stop all services
make test # runs laravel test, generate coverages, and open browse to see how to test integrations
```

## Extending gritzkoo/php-health-checker package

[app/Services/HealthCheckerService.php](app/Services/HealthCheckerService.php "example service")

## Example writing api integrations with test

[app/Api/FakeApi1/FakeApi1.php](app/Api/FakeApi1/FakeApi1.php "example api 1")

[app/Api/FakeApi2/FakeApi2.php](app/Api/FakeApi2/FakeApi2.php "example api 2")
