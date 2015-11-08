<?php namespace App\Http\Controllers\Api;

use App\Services\Jwt;
use App\Http\Controllers\ApiController;
use App\Repositories\User\UserRepository;
use Illuminate\Support\Facades\Auth;


class AuthController extends ApiController {

	/**
	 * Authenticate the api user.
	 * @return mixed
	 */
	public function Auth() {
		try {
			Auth::attempt([
				'username' => trim(strip_tags($this->request->get('username'))),
				'password' => trim(strip_tags($this->request->get('password'))),
			]);
		} catch(\Exception $e) {
		}

		return $this->getJwt();
	}

	/**
	 * Sending a new password to user.
	 *
	 * @param UserRepository $user
	 *
	 * @return mixed
	 */
	public function forgotPassword(UserRepository $user) {
		if($user->forgotPassword($this->request->all())) {
			return $this->response([$this->stringMessage => 'A new password have been sent to you.'], 200);
		}

		return $this->response([$this->stringMessage => "Your email don't exists in our database."], 400);
	}

	/**
	 * Skapar json web token.
	 * @return mixed
	 */
	public function getJwt(){
		if(Auth::check()) {
			return $this->response(['token' => Jwt::encode(array('id' => Auth::user()->id))], 200);
		}
		return $this->response(['message', 'You could not get your auth token, please try agian'], 400);
	}

}
