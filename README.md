# Introduction

- This application comprises of two sections:
   - Task 1 that implements Account Transactions
   - Task 2: Loan Repayment Scheduling

## System Requirements
- PHP 8.0.2
- Laravel 9
- Tested on Ubuntu 22.04
- Database, tested on MySQL & PgSQL

## Installation
- To run this application,
 ```bash
composer i
```
Next, run:

```bash
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
```

Next clear the cache

```bash
  php artisan optimize:clear
 # or
 php artisan config:clear
```

Once done,execute the following commands
```bash
php artisan key:generate
```

To generate the laravel key
Then, install passport by:

```bash
php artisan passport:install
```

Once you have followed the steps above, migrate your tables by running the following commands;
```bash
php artisan migrate
```

The system is role based, hence seed the admin by running the following commands;
```bash
php artisan db:seed
```

Well done, you're almost, just a little more step, we need to send email notifications to our clients,
to notify them on certain state(s) changes.

- Ensure that MAIL is configured on the .env file, see an example how I have done this one:
```dotenv
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=***
MAIL_PASSWORD=*******
MAIL_ENCRYPTION=tls
```

**Note: I have used mails almost in every part of this system and therefore missing mail configs will make all transactions to abort**.

Lastly on configs, ensure you have setup database accordingly;
See an example below:

```dotenv
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pezesha
DB_USERNAME=root
DB_PASSWORD=****
```

## How it works

### Task 1:

