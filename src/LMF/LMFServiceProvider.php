<?php

namespace LMF;

use Illuminate\Support\ServiceProvider;

class LMFServiceProvider extends ServiceProvider {

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
     * Register LMF's migration files.
     *
     * @return void
     */
    protected function registerMigrations()
    {

        if (LMF::$runsMigrations) {
            return $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        }

        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'lmf-migrations');
    }

}