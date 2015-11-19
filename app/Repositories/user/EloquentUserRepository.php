<?php namespace App\Repositories\User;

use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Repositories\CRepository;
use Illuminate\Support\MessageBag;
use App\Services\CollectionService;

/**
 * Class EloquentUserRepository
 * @package App\Repositories\User
 */
class EloquentUserRepository extends CRepository implements UserRepository {

	/**
	 * Fetch one or all users.
	 *
	 * @param null $id
	 *
	 * @return \App\Services\Model|array|\Illuminate\Database\Eloquent\Collection|null|static
	 */
	public function get( $id = null ) {
		if ( is_null( $id ) ) {
			return $this->cache( 'all', User::where( 'id', '!=', 0 ) );
		} else {
			if ( !is_numeric( $id ) ) {
				$id = $this->getIdByUsername( $id );
			}

			return CollectionService::filter( $this->get(), 'id', $id, 'first' );
		}
	}

	/**
	 * Fetch user id by username.
	 *
	 * @param $username
	 *
	 * @return int
	 */
	public function getIdByUsername( $username ) {
		return $this->getIdBy( 'username', $username );
	}

	/**
	 * Fetch user id by email.
	 *
	 * @param $email
	 *
	 * @return int
	 */
	public function getIdByEmail( $email ) {
		return $this->getIdBy( 'email', $email );
	}

	/**
	 * Fetch id by selected field.
	 *
	 * @param $field
	 * @param $value
	 *
	 * @return int
	 */
	private function getIdBy( $field, $value ) {
		$user = CollectionService::filter( $this->get(), $field, $value, 'first' );
		if ( is_null( $user ) ) {
			return 0;
		}

		return $user->id;
	}

	/**
	 * Creates or updates a user.
	 *
	 * @param $input
	 * @param null $id
	 *
	 * @return bool|mixed
	 */
	public function createOrUpdate( $input, $id = null ) {
		if ( !is_numeric( $id ) ) {
			$User = new User;
			if ( isset( $input['username'] ) && $input['username'] != '' ) {
				$User->username = $this->stripTrim( $input['username'] );
			}
			if ( isset( $input['password'] ) && $input['password'] != '' ) {
				$User->password = Hash::make( $input['password'] );
			}
		} else {
			$this->errors = new MessageBag;
			$User = $this->get( $id );
			if ( isset( $input['password'] ) && $input['password'] != '' ) {
				if ( Auth::validate( ['username' => $User->username, 'password' => $input['oldpassword']] ) ) {
					$User->password = Hash::make( $input['password'] );
				} else {
					$this->errors->add( 'oldpassword', 'Your old password was not correct.' );
				}
			}
		}

		if ( count( $this->get() ) == 0 && isset( $input['active'] ) ) {
			$User->active = $this->stripTrim( $input['active'] );
			$id = 1;
		}

		if ( isset( $input['email'] ) ) {
			$User->email = $this->stripTrim( $input['email'] );
		}

		if ( isset( $input['role'] ) ) {
			$User->role = $this->stripTrim( $input['role'] );
		}

		if ( $User->save() ) {
			if ( !is_null( $this->errors ) && $this->errors->first( 'oldpassword' ) != '' ) {
				return false;
			} else {
				if ( is_null( $id ) ) {
					$this->sendActivateMail( $User->id );
				}

				return true;
			}
		} else {
			$this->errors = $User::$errors;
			$checkErrors = key( $this->errors->toArray() );
			if ( !is_array( $checkErrors ) ) {
				$checkErrors = [$checkErrors];
			}
			if ( in_array( 'email', $checkErrors ) ) {
				$olduser = null;
				if ( isset( $input['email'] ) ) {
					$olduser = CollectionService::filter( $this->get(), 'email', $this->stripTrim( $input['email'] ), 'first' );
				}
				if ( !is_null( $olduser ) ) {
					if ( $olduser->active == 0 ) {
						if ( $olduser->delete() ) {
							return $this->createOrUpdate( $input );
						}
					}
				}
			}

			return false;
		}
	}

	/**
	 * Updates a users role or status.
	 *
	 * @param $input
	 * @param $id
	 *
	 * @return bool
	 */
	public function update( $input, $id ) {
		$user = $this->get( $id );
		if ( !is_null( $user ) ) {
			$user->role = $this->stripTrim( $input['role'] );
			$user->active = $this->stripTrim( $input['active'] );
			if ( $user->save() ) {
				return true;
			} else {
				$this->errors = $user::$errors;

				return false;
			}
		}
	}

	/**
	 * Deletes a user.
	 *
	 * @param $id
	 *
	 * @return bool|mixed
	 */
	public function delete( $id ) {
		$user = $this->get( $id );
		if ( !is_null( $user ) ) {
			return $user->delete();
		}

		return false;
	}

	/**
	 * Login a user.
	 *
	 * @param $input
	 *
	 * @return bool
	 */
	public function login( $input ) {
		try {
			return Auth::attempt( [
				'username' => $this->stripTrim( $input['loginUsername'] ),
				'password' => $this->stripTrim( $input['loginpassword'] ),
				'active' => 1,
			] );
		} catch( Exception $e ) {
			return false;
		}
	}

	/**
	 * Sends a new password to user.
	 *
	 * @param $input
	 *
	 * @return bool
	 */
	public function forgotPassword( $input ) {
		$newPassword = $this->createPassword();
		$user = CollectionService::filter( $this->get(), 'email', $this->stripTrim( $input['email'] ), 'first' );
		if ( !is_null( $user ) ) {
			$user->password = $newPassword[1];
			if ( $user->save() ) {
				$data = ['password' => $newPassword[0], 'Username' => $user->username];
				$emailInfo = [
					'toEmail' => $this->stripTrim( $input['email'] ),
					'toName' => $user->username,
					'subject' => 'Forgot password',
				];
				if ( $this->sendEmail( 'emails.forgotPassword', $emailInfo, $data ) ) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Activates user with help of token and id.
	 *
	 * @param $id
	 * @param $token
	 *
	 * @return bool
	 */
	public function activateUser( $id, $token ) {
		$user = $this->get( $id );
		if ( !is_null( $user ) ) {
			if ( $user->active == 0 ) {
				if ( $token == $this->makeToken( $user->email ) ) {
					$user->active = 1;
					if ( $user->save() ) {
						return true;
					}
				}
			}
		}

		return false;
	}

	/**
	 * Creates a new password.
	 *
	 * @return array
	 */
	private function createPassword() {
		$newPassword = [];
		$newPassword[0] = str_random( rand( 7, 10 ) );
		$newPassword[1] = Hash::make( $newPassword[0] );

		return $newPassword;
	}

	/**
	 * Creates a random string.
	 *
	 * @param $email
	 *
	 * @return mixed
	 */
	private function makeToken( $email ) {
		return str_replace( '/', '', md5( $email ) );
	}

	/**
	 * Sends activation mail.
	 *
	 * @param $id
	 *
	 * @return bool
	 */
	private function sendActivateMail( $id ) {
		$user = $this->get( $id );
		$token = $this->makeToken( $user->email );
		$data = ['token' => $token, 'id' => $user->id, 'Username' => $user->username];
		$emailInfo = ['toEmail' => $user->email, 'toName' => $user->username, 'subject' => 'Welcome'];
		if ( $this->sendEmail( 'emails.activateUser', $emailInfo, $data ) ) {
			return true;
		}

		return false;
	}

}