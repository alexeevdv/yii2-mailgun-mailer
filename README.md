# yii2-mailgun-mailer

[![Build Status](https://travis-ci.org/alexeevdv/yii2-mailgun-mailer.svg?branch=master)](https://travis-ci.org/alexeevdv/yii2-mailgun-mailer) 
[![codecov](https://codecov.io/gh/alexeevdv/yii2-mailgun-mailer/branch/master/graph/badge.svg)](https://codecov.io/gh/alexeevdv/yii2-mailgun-mailer)
![PHP 5.6](https://img.shields.io/badge/PHP-5.6-green.svg) 
![PHP 7.0](https://img.shields.io/badge/PHP-7.0-green.svg) 
![PHP 7.1](https://img.shields.io/badge/PHP-7.1-green.svg) 
![PHP 7.2](https://img.shields.io/badge/PHP-7.2-green.svg)


Yii2 mailer implementation that send mails via mailgun.com

## Installation

The preferred way to install this extension is through [composer](https://getcomposer.org/download/).

Either run

```bash
$ php composer.phar require alexeevdv/yii2-mailgun-mailer "~1.0"
```

or add

```
"alexeevdv/yii2-mailgun-mailer": "~1.0"
```

to the ```require``` section of your `composer.json` file.

## Configuration

### Through application component
```php
use alexeevdv\mailer\MailgunMailer;

//...
'components' => [
    //...
    'mailer' => [
        'class' => MailgunMailer::class,
        'apiKey' => 'YOUR_API_KEY',
        'domain' => 'your-domain.tld',
    ],
    //...
],
//...
```
