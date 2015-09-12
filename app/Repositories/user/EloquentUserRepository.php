<?php namespace App\Repositories\User;

use App\Model;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Repositories\CRepository;
use Illuminate\Support\MessageBag;

class EloquentUserRepository extends CRepository implements UserRepository {

	// Hämtar en eller alla användare
	public function get($id = null)
	{
		if(is_null($id)){
			return User::all();
		}else{
			if(!is_numeric($id)){
				$id = $this->getIdByUsername($id);
			}
			return User::find($id);
		}
	}

	// Hämtar användarens id med hjälp av användarnamnet.
	public function getIdByUsername($username){
		return $this->getIdBy('username', $username);
	}

	public function getIdByEmail($email){
		return $this->getIdBy('email', $email);
	}

	private function getIdBy($field, $value){
		$user = User::where($field, '=', $value)->first();
		if(is_null($user)){
			return 0;
		}
		return $user->id;
	}

	// skapar eller uppdaterar en användare
	public function createOrUpdate($input, $id = null)
	{
		if(!is_numeric($id)) {
			$User = new User;
			if(isset($input['username']) && $input['username'] != '') {
				$User->username = $this->stripTrim($input['username']);
			}
			if(isset($input['password']) && $input['password'] != '') {
				$User->password = Hash::make($input['password']);
			}
		} else {
			$this->errors = new MessageBag;
			$User = $this->get($id);
			if(isset($input['password']) && $input['password'] != ''){
				if(Auth::validate(['username' => $User->username, 'password' => $input['oldpassword']])){
					$User->password = Hash::make($input['password']);
				}else{
					$this->errors->add('oldpassword', 'Your old password was not correct.');
				}
			}
		}

		if(count($this->get()) == 0 && isset($input['active'])){
			$User->active = $this->stripTrim($input['active']);
			$id = 1;
		}

		if(isset($input['email'])){
			$User->email = $this->stripTrim($input['email']);
		}

		if(isset($input['role'])){
			$User->role = $this->stripTrim($input['role']);
		}

		if($User->save()){
			if(!is_null($this->errors) && $this->errors->first('oldpassword') != ''){
				return false;
			}else{
				if(is_null($id)){
					$this->sendActivateMail($User->id);
				}
				return true;
			}
		}else{
			$this->errors = $User::$errors;
			$checkErrors = key($this->errors->toArray());
			if(!is_array($checkErrors)){
				$checkErrors = array($checkErrors);
			}
			if(in_array('email', $checkErrors)){
				$olduser = null;
				if(isset($input['email'])) {
					$olduser = User::where('email', '=', $this->stripTrim($input['email']))->first();
				}
				if(!is_null($olduser)){
					if($olduser->active == 0){
						if($olduser->delete()){
							return $this->createOrUpdate($input);
						}
					}
				}
			}
			return false;
		}
	}

	// uppdaterar en användares roll eller status.
	public function update($input, $id){
		$user = $this->get($id);
		if(!is_null($user)){
			$user->role = $this->stripTrim($input['role']);
			$user->active = $this->stripTrim($input['active']);
			if($user->save()){
				return true;
			}else{
				$this->errors = $user::$errors;
				return false;
			}
		}
	}

	// tar bort en användare
	public function delete($id){
		$user = $this->get($id);
		if(!is_null($user)){
			return $user->delete();
		}
		return false;
	}

	// loggar in en användare.
	public function login($input){
		try{
			return Auth::attempt([
				'username' => $this->stripTrim($input['loginUsername']),
				'password' => $this->stripTrim($input['loginpassword']),
				'active' => 1
			]);
		} catch (Exception $e) {
			return false;
		}
	}

	// skickar ett nytt lösenord till användaren.
	public function forgotPassword($input){
		if(Model::Honeypot($input)) {
			$newPassword = $this->createPassword();
			$user = User::where('email', '=', $this->stripTrim($input['email']))->first();
			if(!is_null($user)) {
				$user->password = $newPassword[1];
				if($user->save()) {
					$data = array('password' => $newPassword[0], 'Username' => $user->username);
					$emailInfo = array(
						'toEmail' => $this->stripTrim($input['email']),
						'toName' => $user->username,
						'subject' => 'Forgot password'
					);
					if($this->sendEmail('emails.forgotPassword', $emailInfo, $data)) {
						return true;
					}
				}
			}
		}
		return false;
	}

	// aktiverar en användare.
	public function activateUser($id, $token){
		$user = $this->get($id);
		if(!is_null($user)){
			if($user->active == 0){
				if($token == $this->makeToken($user->email)){
					$user->active = 1;
					if($user->save()){
						return true;
					}
				}
			}
		}
		return false;
	}

	// skapar ett nytt lösenord åt användaren.
	private function createPassword(){
		$newPassword = array();
		$newPassword[0] = str_random(rand (7,10));
		$newPassword[1] = Hash::make($newPassword[0]);
		return $newPassword;
	}

	// skapar den sträng som aktiverar en användare.
	private function makeToken($email){
		return str_replace('/','', md5($email));
	}

	// skickar aktiveringsmejlet.
	private function sendActivateMail($id){
		$user = $this->get($id);
		$token = $this->makeToken($user->email);
		$data = array('token' => $token, 'id' => $user->id, 'Username' => $user->username);
		$emailInfo = array('toEmail' => $user->email, 'toName' => $user->username, 'subject' => 'Welcome');
		if($this->sendEmail('emails.activateUser', $emailInfo, $data)){
			return true;
		}
		return false;
	}

}