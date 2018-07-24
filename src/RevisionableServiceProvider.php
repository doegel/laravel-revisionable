<?php

namespace Revisionable;

use Illuminate\Support\ServiceProvider;

class RevisionableServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->isNotLumen()) {
          $this->publishes([
            __DIR__.'/../config/revisionable.php' => config_path('revisionable.php'),
          ], 'config');
        }

        foreach (config('revisionable.models') as $class) {
            // Install the observer, this drives the actual
            // implementation of this trait.
            $class::observe(RevisionObserver::class);
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->isNotLumen()) {
            $this->mergeConfigFrom(
                __DIR__ . '/../config/revisionable.php',
                'revisionable'
            );
        }
    }

    /**
     * Framework environment check helper.
     *
     * @return bool
     */
    private function isNotLumen(): bool
    {
        return ! stripos(app()->version(), 'lumen');
    }
}
