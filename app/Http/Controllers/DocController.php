<?php namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;

/**
 * Controller for some independent menuoptions.
 *
 * Class DocController
 * @package App\Http\Controllers
 */
class DocController extends Controller
{

    /**
     * Render index view for documentation.
     *
     * @return mixed
     */
    public function index()
    {
        return File::get(storage_path() . '/doc/index.html');
    }

    /**
     * Render api view for documentation.
     *
     * @return mixed
     */
    public function api()
    {
        return File::get(storage_path() . '/doc/api.html');
    }

}
