<?php namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\URL;

/**
 * Class Handler
 * @package App\Exceptions
 */
class Handler extends ExceptionHandler {

	/**
	 * A list of the exception types that should not be reported.
	 *
	 * @var array
	 */
	protected $dontReport = [
		'Symfony\Component\HttpKernel\Exception\HttpException'
	];

	/**
	 * Report or log an exception.
	 *
	 * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
	 *
	 * @param  \Exception  $e
	 * @return void
	 */
	public function report(Exception $e)
	{
		return parent::report($e);
	}

	/**
	 * Render an exception into an HTTP response.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Exception  $e
	 * @return \Illuminate\Http\Response
	 */
	public function render($request, Exception $e) {
		View::share('siteName', ucfirst(str_replace('https://', '', URL::to('/'))));
		if(env('APP_ENV') == 'local') {
			if($this->isHttpException($e)) {
				return $this->renderHttpException($e);
			} else {
				return parent::render($request, $e);
			}
		} else {
			if(method_exists($e, 'getStatusCode')){
				if ( view()->exists( 'errors.' . $e->getStatusCode() ) ) {
					return response()->view( 'errors.' . $e->getStatusCode(), [], $e->getStatusCode() );
				} else {
					return ( new SymfonyDisplayer( config( 'app.debug' ) ) )->createResponse( $e );
				}
			} else {
				return response()->view('errors.404', ['message' => $e->getMessage()]);
			}
		}
	}

}
