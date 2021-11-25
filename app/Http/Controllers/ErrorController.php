<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ErrorController extends Controller
{
    public static function showErrorMessage(array $error): Response
    {
        return Inertia::render(
            'ErrorPages/ErrorMessage',
            [
                'error' => $error,
            ]
        );
    }
}
