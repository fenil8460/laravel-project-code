<?php

namespace Ogt\Developer;

use Illuminate\Support\ServiceProvider;

class DeveloperServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->app->make('Ogt\Developer\DeveloperLibrary');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
