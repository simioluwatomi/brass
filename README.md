## Problem Statement

Create a small set of RESTful API endpoints that allows authorised consumers  
1. Make money transfers using a payment gateway provider in Nigeria (e.g. Paystack, Flutterwave)   
2. List & search their transfer history.

## Implementation

This implementation uses [Paystack](https://paystack.com/docs/) as the payment gateway provider and the Laravel 
framework with the idea of a Double Entry Accounting (DEA) system under the hood. In very simple terms, every debit
transaction entry has a corresponding credit transaction entry and vice versa. So, a transaction is comprised of a 
debit and credit transaction entry pair.

While adding a little complexity, this means that we can handle four transaction scenarios

- An internal account or wallet to another internal account or wallet debit transaction.  
- An internal account or wallet to another internal account or wallet credit transaction.  
- An internal account or wallet to an external account debit transaction.  
- An external account to an internal account or wallet credit transaction.  

To handle scenario C and D above, there is a control account named Nuban Settlement Account. While the problem 
statement only explicitly states scenario B above, I think the others are implied. If I have over-engineered this,
then I apologize; I just couldn't help myself.

## Hosting
I have a Paystack account but with no registered business. So, my secret and public key combination can't
perform transfers. This limitation prevents me from hosting this project. However, I think everything should work 
as the integration is tested to a large extent using mocks and stubs obtained from the Paystack documentation.

## Setup

To set up the project locally,

1. Clone the repository from source control.
-   Navigate to the cloned repo on your computer using the CLI and do the following
    -   Copy example env file to env `cp .env.example .env`.
    -   Generate application key using `php artisan key:generate`.
    -   Create a database in MySQL and enter its credentials in the `.env` file. Note that the project uses a separate
        mysql connection as its test database. You'll need to create the test database if you want to run tests.
    -   Install composer packages using `composer install`.
    -   Migrate the database using `php artisan migrate:fresh --seed`. All seeded users have a password of `password`
        and pin of `1111`.
    -   Start Php's inbuilt server using `php artisan serve`.
    
## Docker
You'd need to consult this [part](https://laravel.com/docs/8.x/sail#installing-composer-dependencies-for-existing-projects)
of the Laravel Sail documentation to get up and running. Also, note that no separate mysql test service has been 
configured. You would need to do that if you want to run tests.

## API Documentation
The API docs is available on [Postman](https://www.getpostman.com/collections/c662cd5884a7b0474d42) but is missing the 
paystack transfer events webhook documentation.
