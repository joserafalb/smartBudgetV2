<?php

namespace Boomingup\CodeHelper;

use Illuminate\Support\ServiceProvider;
use Boomingup\CodeHelper\Controllers\Eloquent\TableController;

class CodeHelperServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->make(TableController::class);
        $this->loadViewsFrom(__DIR__ . '/views', 'code-helper');
        $this->publishes(
            [
                __DIR__ . '/../public' => public_path('boomingup/code-helper'),
            ],
            'public'
        );
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        include __DIR__ . '/routes.php';
    }
}
