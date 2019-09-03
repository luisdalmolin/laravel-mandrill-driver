# Laravel Mandrill Driver

This package re-enables Mandrill driver functionality using teh Mail facade in Laravel 6+.

To install the package in your project you need to require the package via composer:

```bash
composer require therobfonz/laravel-mandrill-driver
```

To add your Mandrill secret key, add teh folling lines to `config\services.php`

```php
'mandrill' => [
    'secret' => env('MANDRILL_KEY'),
],
```

As before, you can set the MAIL_DRIVER value in your env to mandrill to enable it

```php
MAIL_DRIVER=mandrill
```