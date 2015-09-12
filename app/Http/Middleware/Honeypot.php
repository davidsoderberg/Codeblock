<?php namespace App\Http\Middleware;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Closure;

/**
 * Class Role
 * Checks if user has correct role.
 * @package App\Http\Middleware
 */
class Honeypot
{
	private $honeyName = 'honeyName';

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		if(count($request->all()) > 0){
			if(!$this->Honeypot($request->all())){
				return Redirect::back()->with('error', "The ".$this->honeyName." field should be empty.");
			}
		}
		return $next($request);
	}

	private function Honeypot($data = ''){
		if(isset($data[$this->honeyName])) {
			$v = Validator::make(array($this->honeyName => $data[$this->honeyName]), array($this->honeyName => 'honeypot'));
			return $v->passes();
		}
		return true;
	}
}
