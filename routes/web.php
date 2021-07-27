<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BankController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\AccountTypeController;
use App\Http\Controllers\BankAccountController;
use App\Http\Controllers\CategoryTypeController;
use App\Http\Controllers\GoalController;
use App\Http\Controllers\RecurringTransactionsController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('User/Login');
})->name('login');

Route::get('/vuetify', function () {
    return Inertia::render('VuetifyTest');
});

Route::get('/forgot-password', function () {
    return Inertia::render('User/ForgotPassword');
});

Route::get('/register', function () {
    return Inertia::render('User/Register');
});

Route::get('/reset-password', function () {
    return Inertia::render('User/ResetPassword', [
        'email' => $_GET['email'] ?? '',
        'token' => $_GET['token'] ?? ''
    ]);
})->name('password.reset');

// Authenticated links
Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::get('/calendar', function () {
        return Inertia::render('Calendar/Index');
    })->name('calendar');

    /**
     * Resource routes
     */
    Route::resource('banks', BankController::class);
    Route::resource('account-type', AccountTypeController::class);
    Route::resource('bank-accounts', BankAccountController::class);
    Route::resource('category-type', CategoryTypeController::class);
    Route::resource('category', CategoryController::class);
    Route::resource('goal', GoalController::class);
    Route::resource('recurring-transaction', RecurringTransactionsController::class);
});
