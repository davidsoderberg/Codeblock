<?php namespace App\Http\Middleware;

use Closure;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Session\TokenMismatchException;

/**
 * Class VerifyCsrf
 * @package App\Http\Middleware
 */
class VerifyCsrf extends VerifyCsrfToken
{

    /**
     * Handle for VerifyCsrf.
     *
     * @param \Illuminate\Http\Request $request
     * @param callable $next
     * @return \Illuminate\Http\Response
     * @throws TokenMismatchException
     */
    public function handle($request, Closure $next)
    {
        if ($this->isReading($request) || ($request->is('api/*') || $request->is('pusher/*') )|| $this->tokensMatch($request)) {
            return $this->addCookieToResponse($request, $next($request));
        }
        throw new TokenMismatchException;
    }
}
