<?php namespace App\Http\Middleware;

use Closure;


/**
 * Class Api
 * Adding allow origin to alla api requests.
 * @package App\Http\Middleware
 */
class Api
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
		// Headers to add.
		$headers = [
			'Access-Control-Allow-Origin' => '*',
			'Access-Control-Allow-Methods'=> 'POST, GET, PUT, DELETE',
			'Access-Control-Allow-Headers'=> 'Content-Type, X-Auth-Token, Origin'
		];

		// Fetch the response.
		$response = $next($request);

		// Adding headers to response.
		foreach($headers as $key => $value) {
			$response->header($key, $value);
		}

		return $response;
	}
}
