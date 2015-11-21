<?php namespace App\Models\Traits;

/**
 * Class TeamTrait
 * @package App\Models\Traits
 */
trait TeamTrait
{

	/**
	 * Fetch invites this team has many of.
	 *
	 * @return mixed
	 */
	public function invites()
	{
		return $this->hasMany( 'App\Models\TeamInvite', 'team_id', 'id');
	}

	/**
	 * Fetch posts this team has many of.
	 *
	 * @return mixed
	 */
	public function posts(){
		return $this->hasMany( 'App\Models\Post', 'team_id', 'id');
	}

	/**
	 * Fetch users this team belongs to many of.
	 *
	 * @return mixed
	 */
	public function users()
	{
		return $this->belongsToMany('App\Models\User', 'team_user', 'team_id','user_id');
	}

	/**
	 * Fetch owner this team has one of.
	 *
	 * @return mixed
	 */
	public function owner()
	{
		return $this->hasOne('App\Models\User', 'id', "owner_id");
	}

	/**
	 * Checks if this team has users.
	 *
	 * @param Model $user
	 *
	 * @return bool
	 */
	public function hasUser( Model $user )
	{
		return $this->users()->where( $user->getKeyName(), "=", $user->getKey() )->first() ? true : false;
	}

}