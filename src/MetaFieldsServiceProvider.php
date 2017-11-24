<?php

namespace Sackrin\Meta;

use Illuminate\Support\ServiceProvider;

class MetaFieldsServiceProvider extends ServiceProvider {

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'lmf');

        if ($this->app->runningInConsole()) {

            $this->registerMigrations();
        }
    }

    /**
     * Register Sackrin\Meta's migration files.
     *
     * @return void
     */
    protected function registerMigrations()
    {

        if (MetaFields::$runsMigrations) {
            return $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        }

        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'lmf-migrations');
    }

}