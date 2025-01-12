Crypto
======

Test project for get the Bitcoin cryptocurrency rate.

Set up
-------------
1. Clone repository
```shell
git clone <url_rep> my_project
cd my_project
```
2. Build Project
```shell
docker-compose up -d
docker-compose exec app composer install
docker-compose exec app php bin/console doctrine:migrations:migrate
```
3. Set up CRON command
```shell
docker-compose exec app php bin/console app:update-rates
```

Example
-------------

API endpoint:

GET /api/binance/rates - getting currency rates from Binance portal

Parameters:

currency_pairs: BTCUSD, BTCEUR, BTCGBP
start_date: timestamp of starting period
end_date: timestamp of ending period

Request example:
GET /api/binance/rates?currency_pairs=BTCUSD,BTCEUR,BTCGBP&from=2024-01-08T00:00:00&to=2024-01-09T00:00:00
