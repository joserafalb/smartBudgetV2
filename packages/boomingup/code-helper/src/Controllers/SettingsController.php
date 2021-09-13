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

namespace Boomingup\CodeHelper\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * SettingsController class
 */
class SettingsController extends Controller
{
    /**
     * Display Settings page
     *
     * @param Request $request Laravel request
     *
     * @return void
     */
    public function index(Request $request)
    {
        return view(
            'code-helper::layout.sections.settings.index',
            [
                'currentFolder' => $request->session()->get('modelsFolder', '')
            ]
        );
    }

    /**
     * Save settings
     *
     * @param Request $request Laravel request
     *
     * @return void
     */
    public function save(Request $request)
    {
        $request->session()->put('modelsFolder', $request->modelsFolder);
        return view(
            'code-helper::layout.sections.settings.index',
            [
                'currentFolder' => $request->session()->get('modelsFolder', '')
            ]
        );
    }
}
