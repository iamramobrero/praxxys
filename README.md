#PRAXXYS BACKEND EXAM

##Requirements
PHP **8.1.0** is required to run Laravel 10
Please install **composer** to download the PHP libraries
Please install **NPM** to download JS Libraries

##Installation
Create your `.env` file in the root folder and update the database connection accordingly. You may copy `env.example` as template
Run `composer install` to download PHP libaries
Run `npm install` to download JS libaries
Run `php artisan passport:install` to install **Laravel Passport** for API Authentication


##Seeding
Run `php artisan migrate:refresh --seed` to generate and fill database

