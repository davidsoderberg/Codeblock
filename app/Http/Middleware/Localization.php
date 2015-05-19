<?php namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;

/**
 * Class Localization
 * Check what lang should be used on the website.
 * @package App\Http\Middleware
 */
class Localization {

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		$langcode = explode('.', $request->server('HTTP_HOST'))[0];
		if ( in_array($langcode, config('languages', ['en'])) ){
			App::setLocale($langcode);
		}

		return $next($request);
	}
}