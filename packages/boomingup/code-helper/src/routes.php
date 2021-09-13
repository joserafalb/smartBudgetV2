<?php

use Boomingup\CodeHelper\Controllers\SettingsController;
use Boomingup\CodeHelper\Controllers\Laravel\MenuController;
use Boomingup\CodeHelper\Controllers\Eloquent\TableController;

Route::group(
    [
        'prefix' => 'code-helper',
        'middleware' => ['web']
    ],
    function () {
        Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
        Route::post('/settings', [SettingsController::class, 'save'])->name('settings');

        Route::group(
            [
                'prefix' => 'laravel'
            ],
            function () {
                Route::get('/', [MenuController::class, 'index'])->name('laravel.menu');

                Route::group(
                    [
                        'prefix' => 'eloquent'
                    ],
                    function () {
                        Route::get('/', [TableController::class, 'index'])->name('laravel.eloquent.crud');
                        Route::post('/', [TableController::class, 'edit']);
                    }
                );
            }
        );
    }
);
