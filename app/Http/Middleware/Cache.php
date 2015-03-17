<?php namespace App\Http\Middleware;

use Closure;

class Cache
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
		$response = $next($request);
		$response->header('Cache-Control', 'public');
		$response->setLastModified(new \DateTime("now"));
		$response->setExpires(new \DateTime("tomorrow"));

		return $response;
	}
}
