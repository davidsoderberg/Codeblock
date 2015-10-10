<?php namespace App\ModelTraits;

trait TeamInviteTrait
{

	public function team()
	{
		return $this->hasOne( 'App\Team', 'id', 'team_id' );
	}

	public function user()
	{
		return $this->hasOne( 'App\User', 'email', 'email' );
	}

}