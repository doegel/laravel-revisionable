# 🗂 laravel-revisionable [![Build Status](https://travis-ci.org/doegel/laravel-revisionable.svg?branch=master)](https://travis-ci.org/doegel/laravel-revisionable)
Drop-in module to keep revisions of Eloquent models.

## Usage

In your project:
```composer require doegel/laravel-revisionable```

Next, publish the package default config:

```
php artisan vendor:publish --provider="Revisionable\RevisionableServiceProvider" --tag="config"
```

## Tests

1. Spin up docker `docker-compose up -d`
2. Run `vendor/bin/phpunit` in project root