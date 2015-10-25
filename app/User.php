<?php namespace App;

use App\ModelTraits\UserHasTeamsTrait;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract {

	use Authenticatable, CanResetPassword, UserHasTeamsTrait;

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
			foreach($object->socials as $social){
				$social->delete();
			}
			foreach($object->reads as $read){
				$read->delete();
			}
	    });
	}

	protected $modelsToReload = [
		'App\Post',
		'App\Comment',
		'App\Star',
		'App\Rate',
		'App\Social',
		'App\Read',
		'App\Notification'
	];

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

	//protected $hidden = array('password');

	protected $fillable = array('username', 'email', 'role', 'active');

	protected $guarded = array('id', 'password');

	protected $addHidden = array('password', 'remember_token', 'role', 'isactive');

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

	public function reads() {
		return $this->hasMany('App\Read', 'user_id', 'id');
	}

	public function hasRead($topic_id){
		foreach($this->reads as $read){
			if($read->topic_id == $topic_id){
				return true;
			}
		}
		return false;
	}

	public function rates() {
		return $this->hasMany('App\Rate', 'user_id', 'id');
	}

	public function socials() {
		return $this->hasMany('App\Social', 'user_id', 'id');
	}

	public function getrolenameAttribute()
	{
		if(!is_null($this->roles)) {
			return $this->roles->name;
		}
	}

	public function getisactiveAttribute(){
		return $this->getAnswer($this->active);
	}

	public function getlinksAttribute(){
		return $this->hateoas($this->id, 'users');
	}

	protected $appends = array('rolename', 'isactive');

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

	public function hasPermission($permission, $empty = true) {
		if($permission != '') {
			$permissions_array = array();
			foreach(Auth::user()->roles->permissions as $user_permission) {
				$permissions_array[] = strtolower($user_permission->permission);
			}
			if(is_array($permission)){
				$permission = $permission[0];
			}

			$permission = explode(':', $permission);
			return in_array(strtolower($permission[0]), $permissions_array);
		}
		return $empty;
	}

	public function hasStarMarkedPosts(){
		foreach($this->posts as $post) {
			if($post->starcount > 0) {
				return true;
			}
		}
		return false;
	}

	public function showOnly(){
		return Session::has('only');
	}

	public function setOnly(){
		if(Session::has('only')){
			Session::forget('only');
			return;
		}
		Session::put('only', 'yes');
	}

}