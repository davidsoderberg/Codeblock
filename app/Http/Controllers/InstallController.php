<?php namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use App\Services\Env;

/**
 * Class InstallController
 * @package App\Http\Controllers
 */
class InstallController extends Controller
{

    /**
     * Runs install.
     *
     * @return mixed
     */
    public function install(\Illuminate\Foundation\Application $app)
    {
        $env = new Env($app);
        $envArray = $env->getKeyValus(true);
        $options = [];
        foreach ($envArray as $key => $value) {
            if ($value == 'null') {
                $value = '';
            }
            $options[$key] = $value;
        }
        return View::make('install')->with('title', 'Install')->with('options', $options);
    }

    /**
     * Stores installation options.
     *
     * @return mixed
     */
    public function store(\Illuminate\Foundation\Application $app)
    {

        $env = new Env($app);
        //$env->save($this->request->get('env'));

        $error = true;

        if (empty($error)) {
            return Redirect::to('/')->with('success', 'You have now installed codeblock.');
        } else {
            if (is_numeric($error) || is_bool($error)) {
                $error = 'We could not install codeblock for some reason, please try agian.';
            }
            return Redirect::back()->with('error', $error)->withInput($this->request->all());
        }
    }
}
