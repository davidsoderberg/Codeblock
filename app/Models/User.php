<?php namespace App\Models;

use App\Models\Traits\Messagable;
use App\Models\Traits\UserHasTeamsTrait;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

/**
 * Class User
 * @package App\Models
 */
class User extends Model implements AuthenticatableContract, CanResetPasswordContract
{

	use Authenticatable, CanResetPassword, UserHasTeamsTrait, Messagable;

	/**
	 * Boot method for User model.
	 */
	public static function boot()
	{
		parent::boot();
		static::deleting(function ($object) {
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
			foreach ($object->socials as $social) {
				$social->delete();
			}
			foreach ($object->reads as $read) {
				$read->delete();
			}
		});
	}

	/**
	 * Array with models to reload on save.
	 *
	 * @var array
	 */
	protected $modelsToReload = [
		'App\Models\Post',
		'App\Models\Comment',
		'App\Models\Star',
		'App\Models\Rate',
		'App\Models\Social',
		'App\Models\Read',
		'App\Models\Notification',
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

	protected $hidden = ['password', 'remember_token'];

	/**
	 * Array with fields that user are allowed to fill.
	 *
	 * @var array
	 */
	protected $fillable = ['username', 'email', 'role', 'active'];

	/**
	 * Array with fields that are guarded.
	 *
	 * @var array
	 */
	protected $guarded = ['id', 'password'];

	/**
	 * Array with fields to add to hidden array.
	 *
	 * @var array
	 */
	protected $addHidden = ['password', 'remember_token', 'role', 'isactive'];

	/**
	 * Array with rules for fields.
	 *
	 * @var array
	 */
	public static $rules = [
		'email' => 'required|email|unique:users,email,:id:',
		'username' => 'required|alpha_dash|unique:users,username,:id:',
		'password' => 'required',
		'role' => 'integer',
		'active' => 'integer',
	];

	/**
	 * Get the unique identifier for the user.
	 *
	 * @return mixed
	 */
	public function getAuthIdentifier()
	{
		return $this->getKey();
	}

	/**
	 * Get the password for the user.
	 *
	 * @return string
	 */
	public function getAuthPassword()
	{
		return $this->password;
	}

	/**
	 * Get the e-mail address where password reminders are sent.
	 *
	 * @return string
	 */
	public function getReminderEmail()
	{
		return $this->email;
	}

	/**
	 * Fetch unread notification this user have.
	 *
	 * @return mixed
	 */
	public function unread()
	{
		return $this->inbox()->where('is_read', '=', 0);
	}

	/**
	 * Fetch sended notification this user have.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function outbox()
	{
		return $this->hasMany('App\Models\Notification', 'from_id', 'id');
	}

	/**
	 * Fetch recieved notification this user have.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function inbox()
	{
		return $this->hasMany('App\Models\Notification', 'user_id', 'id');
	}

	/**
	 * Fetch posts this user has many of.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function posts()
	{
		return $this->hasMany('App\Models\Post', 'user_id', 'id');
	}

	/**
	 * Fetch stars this user has many of.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function stars()
	{
		return $this->hasMany('App\Models\Star', 'user_id', 'id');
	}

	/**
	 * Fetch comments this user has many of.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function comments()
	{
		return $this->hasMany('App\Models\Comment', 'user_id', 'id');
	}

	/**
	 * Fetch posts this user has one.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasOne
	 */
	public function roles()
	{
		return $this->hasOne('App\Models\Role', 'id', 'role');
	}

	/**
	 * Fetch reads this user has many of.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function reads()
	{
		return $this->hasMany('App\Models\Read', 'user_id', 'id');
	}

	/**
	 * Checks if user has reads.
	 *
	 * @param $topic_id
	 *
	 * @return bool
	 */
	public function hasRead($topic_id)
	{
		foreach ($this->reads as $read) {
			if ($read->topic_id == $topic_id) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Fetch rates this user has many of.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function rates()
	{
		return $this->hasMany('App\Models\Rate', 'user_id', 'id');
	}

	/**
	 * Fetch socials this user has many of.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function socials()
	{
		return $this->hasMany('App\Models\Social', 'user_id', 'id');
	}

	/**
	 * Fetch role name for the role this user has.
	 *
	 * @return mixed
	 */
	public function getrolenameAttribute()
	{
		if (!is_null($this->roles)) {
			return $this->roles->name;
		}
	}

	/**
	 * Fetch yes and no for active.
	 *
	 * @return string
	 */
	public function getisactiveAttribute()
	{
		return $this->getAnswer($this->active);
	}

	/**
	 * Fetch hateoas link for api.
	 *
	 * @return array
	 */
	public function getlinksAttribute()
	{
		return $this->hateoas($this->id, 'users');
	}

	/**
	 * Fetch teams for this user.
	 *
	 * @return mixed
	 */
	public function getteamsAttribute()
	{
		return $this->teams()->get()->merge($this->ownedTeams()->get());
	}

	/**
	 * Appends an array of attributes on model.
	 *
	 * @var array
	 */
	protected $appends = ['rolename', 'isactive', 'teams'];


	/**
	 * Checks if social is connected with user.
	 *
	 * @param $social
	 *
	 * @return bool
	 */
	public function hasSocial($social)
	{
		$socials = $this->socials;
		foreach ($socials as $soc) {
			if ($social == $soc->social) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Checks if use has role.
	 *
	 * @param $roles
	 *
	 * @return bool
	 */
	public function hasRole($roles)
	{

		if (!is_array($roles)) {
			$roles = [strtolower($roles)];
		} else {
			foreach ($roles as $key => $value) {
				$roles[$key] = strtolower($value);
			}
		}

		return in_array(strtolower(Auth::user()->role), $roles);
	}

	/**
	 * Checks if user has permission.
	 *
	 * @param $permission
	 * @param bool|true $empty
	 *
	 * @return bool
	 */
	public function hasPermission($permission, $empty = true)
	{
		if ($permission != '') {
			$permissions_array = [];
			foreach (Auth::user()->roles->permissions as $user_permission) {
				$permissions_array[] = strtolower($user_permission->permission);
			}
			if (is_array($permission)) {
				$permission = $permission[0];
			}

			$permission = explode(':', $permission);

			return in_array(strtolower($permission[0]), $permissions_array);
		}

		return $empty;
	}

	/**
	 * Checks if user has starred posts.
	 *
	 * @return bool
	 */
	public function hasStarMarkedPosts()
	{
		foreach ($this->posts as $post) {
			if ($post->starcount > 0) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Checks if user only would like to see it owns codeblocks.
	 *
	 * @return mixed
	 */
	public function showOnly()
	{
		return Session::has('only');
	}

	/**
	 * Setter for only session.
	 */
	public function setOnly()
	{
		if (Session::has('only')) {
			Session::forget('only');

			return;
		}
		Session::put('only', 'yes');
	}

}