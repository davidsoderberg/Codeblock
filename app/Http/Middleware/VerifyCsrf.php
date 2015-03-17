<?php namespace App\Http\Middleware;

use Closure;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Session\TokenMismatchException;

class VerifyCsrf extends VerifyCsrfToken {

	/**
	 * @param \Illuminate\Http\Request $request
	 * @param callable $next
	 * @return \Illuminate\Http\Response
	 * @throws TokenMismatchException
	 */
	public function handle($request, Closure $next)
	{
		if ($this->isReading($request) || $request->is('api/*') || $this->tokensMatch($request)) {
			return $this->addCookieToResponse($request, $next($request));
		}
		throw new TokenMismatchException;
	}

}
