<?php namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class Installed
{

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		try {
			DB::connection()->getDatabaseName();
		} catch(\Exception $e) {
			if($request->server('PATH_INFO') != '/install') {
				return Redirect::to('/install');
			}
		}
		return $next($request);
	}
}
