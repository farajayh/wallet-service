# Wallet-Service
Wallet API Service

Wallet API service for an e-comerce application.

## Features:

* Cutomer CRUD

* Merchant CRUD

* Wallet Creation

* View wallet details

* Debiting of wallet

* Crediting of wallet

* View wallet transaction history

* CRON script that runs daily to check wallet transaction history and compares it with wallet balance for inconsistencies.
Records of inconsistent wallets are stored on a CSV file in storage/app folder.

## Assumptions
It is assumed that the e-commerce application has an authentication service that issues authentication tokens for authenticating requests to this service.
Requests to this service will be checked for authentication token and the token will be validated using the key of the auth service, but that is not covered here.

## Database ERD
![Database_ERD.png](https://github.com/farajayh/wallet-service/blob/main/Database_ERD.png?raw=true)
The database ERD is located at the repository root as Database_ERD.png

## API Documentation
https://documenter.getpostman.com/view/9782302/2sA3s4mVkv

* The postman collection is located at the repository root as wallet-service.postman_collection.json

## HOW TO RUN
- Clone this repository locally: git clone https://github.com/farajayh/wallet-service.git
- CD into the application directory
- Create the .env file for environment variables: cp .env.example .env or copy .env.example .env
- Install dependencies: composer install
- Generate application key: php artisan key:generate
- Make sure MySQL is running on your machine
- Enter the MySQL credentials in the .env file
- Create wallet-service database on your MySQL server
- Run database migration: php artisan migrate
- To run tests, do: php artisan test
- To seed the database: php artisan db:seed
- Run the application: php artisan serve
- To schedule running of CRON job, create this cron job entry: * * * * * cd /path-to-the-application-directory && php artisan schedule:run >> /dev/null 2>&1
- The CRON job can also be run manually by running: php artisan wallet:verify-wallets-balance

## Technologies Used
- PHP 8.2
- MySQL
- Laravel 11
- PHPUnit