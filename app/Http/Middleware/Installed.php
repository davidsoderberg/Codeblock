<?php namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;

/**
 * Class Installed
 * Checks if codeblock is installed.
 * @package App\Http\Middleware
 */
class Installed
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        try {
            DB::connection()->getDatabaseName();
            if (Str::contains($request->route()->getAction()['uses'], 'InstallController')) {
                return Redirect::action('MenuController@index');
            }
        } catch (\Exception $e) {
            if (!Str::contains($request->route()->getAction()['uses'], 'InstallController')) {
                return Redirect::action('InstallController@install');
            }
        }
        return $response;
    }
}
