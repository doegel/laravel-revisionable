# 🗂 laravel-revisionable
Drop-in module to keep revisions of Eloquent models.

## Usage

In your project:
```composer require doegel/laravel-revisionable```

Next, publish the package default config:

```
php artisan vendor:publish --provider="Revisionable\RevisionableServiceProvider" --tag="config"
```

Then generate the include file with
```
php artisan vue-i18n:generate
```