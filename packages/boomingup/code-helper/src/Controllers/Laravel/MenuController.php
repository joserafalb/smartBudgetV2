<?php

/**
 * App\Http\Controllers\Eloquent
 *
 * PHP Version 7.3
 *
 * @category CategoryName
 * @package  App\Http\Controllers\Eloquent
 * @author   Jose Lopez <joserafalb@gmail.com>
 * @license  Open source
 * @link     http://pear.php.net/package/PackageName
 */

namespace Boomingup\CodeHelper\Controllers\Laravel;

use App\Http\Controllers\Controller;

/**
 * MenuController class
 */
class MenuController extends Controller
{
    /**
     * MenuController Index
     *
     * @return void
     */
    public function index()
    {
        return view('code-helper::layout.sections.laravel.menu');
    }
}
