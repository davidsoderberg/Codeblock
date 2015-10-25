<?php namespace App\ModelTraits;

trait TeamTrait
{

	public function invites()
	{
		return $this->hasMany( 'App\TeamInvite', 'team_id', 'id');
	}

	public function posts(){
		return $this->hasMany( 'App\Post', 'team_id', 'id');
	}

	public function users()
	{
		return $this->belongsToMany('App\User', 'team_user', 'team_id','user_id');
	}

	public function owner()
	{
		return $this->hasOne('App\User', 'id', "owner_id");
	}

	public function hasUser( Model $user )
	{
		return $this->users()->where( $user->getKeyName(), "=", $user->getKey() )->first() ? true : false;
	}

}