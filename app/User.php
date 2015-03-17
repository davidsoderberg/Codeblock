<?php namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Support\Facades\Auth;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract {

	use Authenticatable, CanResetPassword;

	public static function boot() {
	    parent::boot();
	    static::deleting(function($object) {
	    	foreach ($object->posts as $post) {
				$post->delete();
			}
	        foreach ($object->comments as $comment) {
				$comment->delete();
			}
			foreach ($object->stars as $star) {
				$star->delete();
			}
	        foreach ($object->rates as $rate) {
				$rate->delete();
			}
	    });
	}

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */

	// protected $hidden = array('password');

	protected $fillable = array('username', 'email', 'role', 'active');

	protected $guarded = array('id', 'password');

	public static $rules = array(
	    'email'  => 'required|email|unique:users,email,:id:',
	    'username' => 'required|alpha_dash|unique:users,username,:id:',
	    'password' => 'required',
	    'role' => 'integer',
	    'active' => 'integer'
	);

	/**
	 * Get the unique identifier for the user.
	 *
	 * @return mixed
	 */
	public function getAuthIdentifier() {
		return $this->getKey();
	}

	/**
	 * Get the password for the user.
	 *
	 * @return string
	 */
	public function getAuthPassword() {
		return $this->password;
	}

	/**
	 * Get the e-mail address where password reminders are sent.
	 *
	 * @return string
	 */
	public function getReminderEmail() {
		return $this->email;
	}

	public function unread() {
		return $this->inbox()->where('is_read', '=', 0);
	}

	public function outbox() {
		return $this->hasMany('App\Notification', 'from_id', 'id');
	}

	public function inbox() {
		return $this->hasMany('App\Notification', 'user_id', 'id');
	}

	public function posts() {
		return $this->hasMany('App\Post', 'user_id', 'id');
	}

	public function stars() {
		return $this->hasMany('App\Star', 'user_id', 'id');
	}

	public function comments() {
		return $this->hasMany('App\Comment', 'user_id', 'id');
	}

	public function roles() {
		return $this->hasOne('App\Role', 'id', 'role');
	}

	public function rates() {
		return $this->hasMany('App\Rate', 'user_id', 'id');
	}

	public function socials() {
		return $this->hasMany('App\Social', 'user_id', 'id');
	}

	public function hasSocial($social) {
		$socials = $this->socials;
		foreach($socials as $soc){
			if($social == $soc->social) {
				return true;
			}
		}
		return false;
	}

	public function hasRole($roles) {

		if(!is_array($roles)){
			$roles = array(strtolower($roles));
		}else{
			foreach($roles as $key => $value){
				$roles[$key] = strtolower($value);
			}
		}

		return in_array(strtolower(Auth::user()->role), $roles);
	}

	public function hasPermission($permissions) {
		$allowed = false;

		if(!is_array($permissions)){
			$permissions = array($permissions);
		}

		$user_permissions = Auth::user()->roles()->permissions;
		$permissions_array = array();

		foreach ($user_permissions as $user_permission) {
			$permissions_array[] = strtolower($user_permission->permission);
		}

		foreach ($permissions as $permission) {
			if(!$allowed){
				$allowed = in_array(strtolower($permission), $permissions_array);
			}
		}
		return $allowed;
	}

}