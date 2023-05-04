# Laravel Mandrill Driver

This package re-enables Mandrill driver functionality using the Mail facade in Laravel 6+.

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


#### Accessing Mandrill message ID
Mandrill message ID's for sent emails can be accessed by listening to the `MessageSent` event. It can then be read either from the sent data or the X-Message-ID header.

```php

Event::listen(\Illuminate\Mail\Events\MessageSent::class, function($event)
{
    $messageId = $event->sent->getMessageId();
    $messageId = $event->message->getHeaders()->get('X-Message-ID');
}
 
```

## Versions

| Laravel Version  | Mandrill package version         |
|------------------|----------------------------------|
| 10               | 5.x                              |
| 9                | 4.x                              |
| 6, 7, 8          | 3.x                              |

## Laravel 7+ Installation

```bash
composer require therobfonz/laravel-mandrill-driver:^3.0
```

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
