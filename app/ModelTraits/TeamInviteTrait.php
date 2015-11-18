<?php namespace App\ModelTraits;

/**
 * Class TeamInviteTrait
 * @package App\ModelTraits
 */
trait TeamInviteTrait
{

	/**
	 * Fetch invites team.
	 *
	 * @return mixed
	 */
	public function team()
	{
		return $this->hasOne( 'App\Team', 'id', 'team_id' );
	}

	/**
	 * Fetch invites user.
	 *
	 * @return mixed
	 */
	public function user()
	{
		return $this->hasOne( 'App\User', 'email', 'email' );
	}

}