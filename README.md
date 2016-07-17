# Laravel 4.2 starter template

Kickstart your laravel project and save time with this starter template.
There is no anything extra, not even css styling.

## Requirements
* PHP >= 5.4
* MCrypt PHP Extension

## Features
#### Multiauth
+ Admin panel
+ Members panel

#### Login
+ Limit login attempts
+ Captcha

#### Register
+ Captcha
+ Email account verify link

#### Account recovery
+ Captcha
+ One hour recovery link expiry

#### Members panel
+ Change password
+ Logout

#### Admin panel
+ Logout

## Installation
* Clone or download repository
* Navigate to folder
* Enter your DB connection settings at
```
root/app/config/database.php
```
* Enter your email connection settings at
```
root/app/config/mail.php
```
* Run
```
composer update
php artisan migration
php artisan db:seed
```
* Enter your no-reply email address and name at 
```
root/app/controllers/AuthController.php
```
* Get reCaptcha keys and enter those at
```
root/.env.php
```
* Done!

## Thanks
https://github.com/ollieread/multiauth  
https://github.com/ARCANEDEV/noCAPTCHA

## License
MIT License - see the LICENSE file for details

