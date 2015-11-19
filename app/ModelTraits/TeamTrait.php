<?php namespace App\ModelTraits;

/**
 * Class TeamTrait
 * @package App\ModelTraits
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
		return $this->hasMany( 'App\TeamInvite', 'team_id', 'id');
	}

	/**
	 * Fetch posts this team has many of.
	 *
	 * @return mixed
	 */
	public function posts(){
		return $this->hasMany( 'App\Post', 'team_id', 'id');
	}

	/**
	 * Fetch users this team belongs to many of.
	 *
	 * @return mixed
	 */
	public function users()
	{
		return $this->belongsToMany('App\User', 'team_user', 'team_id','user_id');
	}

	/**
	 * Fetch owner this team has one of.
	 *
	 * @return mixed
	 */
	public function owner()
	{
		return $this->hasOne('App\User', 'id', "owner_id");
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