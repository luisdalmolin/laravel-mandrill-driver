# Laravel Mandrill Driver

This package re-enables Mandrill driver functionality using the Mail facade in Laravel 6+ and Lumen.

To install the package in your project, you need to require the package via Composer:

```bash
composer require therobfonz/laravel-mandrill-driver
```

To add your Mandrill secret key, add the following lines to `config\services.php` and set `MANDRILL_KEY` in your env:

```php
'mandrill' => [
    'secret' => env('MANDRILL_KEY'),
],
```

You can also add custom Mandrill headers to each email sent, for this you need to add the headers array in the following format to `config\services.php`:

```php
'mandrill' => [
    'secret' => env('MANDRILL_KEY'),
    'headers' => [
        'header-example-x' => env('MANDRILL_HEADER_X'),
        'header-example-y' => env('MANDRILL_HEADER_Y'),
    ]
],
```
all the valid options in Mandrill docs at: https://mailchimp.com/developer/transactional/docs/smtp-integration/#customize-messages-with-smtp-headers

## Laravel 7+ Installation

Add the Mandrill mailer to your `config\mail.php`:

```php
'mandrill' => [
    'transport' => 'mandrill',
],
```

Set the `MAIL_MAILER` value in your env to `mandrill` to enable it:

```php
MAIL_MAILER=mandrill
```

## Laravel 6 Installation

As before, you can set the `MAIL_DRIVER` value in your env to `mandrill` to enable it:

```php
MAIL_DRIVER=mandrill
```

## Lumen Installation

Add the following line to `bootstrap/app.php`

```php
$app->register(LaravelMandrill\MandrillServiceProvider::class);
```