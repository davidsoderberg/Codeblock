<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Repositories\User\UserRepository;
use Illuminate\Support\Facades\Auth;

/**
 * Class UserController
 * @package App\Http\Controllers\Api
 */
class UserController extends ApiController {

	/**
	 * Shows a user.
	 *
	 * @param UserRepository $user
	 * @param null $id
	 *
	 * @return mixed
	 */
	public function Users(UserRepository $user, $id = null) {
		return $this->response( [$this->stringData => $this->getCollection( $user, $id )], 200 );
	}

	/**
	 * Creating or updating a user.
	 *
	 * @param UserRepository $user
	 * @param null $id
	 *
	 * @return mixed
	 */
	public function createOrUpdateUser(UserRepository $user, $id = null) {
		if(!is_null($id)) {
			if($id != Auth::user()->id && !Auth::user()->hasPermission('update_users', false)) {
				return $this->response([$this->stringErrors => [$this->stringUser => 'You are not that user']], 400);
			}
		}
		if($user->createOrUpdate($this->request->all(), $id)) {
			if(is_null($id)) {
				return $this->response([$this->stringMessage => 'Your user has been created, use the link in the mail to activate your user.'], 201);
			} else {
				return $this->response([$this->stringMessage => 'Your user has been saved.'], 201);
			}
		}

		return $this->response([$this->stringErrors => $user->getErrors()], 400);
	}

}
