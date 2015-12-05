<?php namespace App\Models\Traits;

use App\Models\Team;

/**
 * Class UserHasTeamsTrait
 * @package App\Models\Traits
 */
trait UserHasTeamsTrait
{

	/**
	 * Fetch teams this user belongs to many.
	 *
	 * @return mixed
	 */
	public function teams()
	{
		return $this->belongsToMany( 'App\Models\Team','team_user', 'user_id', 'team_id' );
	}

	/**
	 * Fetch teams this user owns many of.
	 *
	 * @return mixed
	 */
	public function ownedTeams()
	{
		return $this->hasMany( 'App\Models\Team', 'owner_id', 'id' );
	}

	/**
	 * Fetch invites this user has many of.
	 *
	 * @return mixed
	 */
	public function invites()
	{
		return $this->hasMany( 'App\Models\TeamInvite', 'email', 'email' );
	}

	/**
	 * Boot method for this trait.
	 */
	public static function bootUserHasTeams()
	{
		static::deleting( function ( Model $user )
		{
			$user->teams()->sync( [ ] );
			return true;
		} );
	}

	/**
	 * Attach team to this user.
	 *
	 * @param $team
	 *
	 * @return $this
	 */
	public function attachTeam( $team )
	{
		$teamId = $this->retrieveTeamId( $team );
		if( !$this->teams()->get()->contains( $team ) )
		{
			$this->teams()->attach( $teamId );
		}
		return $this;
	}

	/**
	 * Fetch team id.
	 *
	 * @param $team
	 *
	 * @return mixed
	 */
	protected function retrieveTeamId( $team )
	{
		if ( is_object( $team ) )
		{
			$team = $team->getKey();
		}
		if ( is_array( $team ) && isset( $team[ "id" ] ) )
		{
			$team = $team[ "id" ];
		}
		return $team;
	}

	/**
	 * Checks if this user is owner of team.
	 *
	 * @param $team
	 *
	 * @return bool
	 */
	public function isOwnerOfTeam( $team )
	{
		$teamId        = $this->retrieveTeamId( $team );
		$teamKeyName = ( new Team() )->getKeyName();
		return ( ( new Team() )
			->where( "owner_id", "=", $this->getKey() )
			->where( $teamKeyName, "=", $teamId )->first()
		) ? true : false;
	}

	/**
	 * Attach teams to this user.
	 *
	 * @param $teams
	 */
	public function attachTeams( $teams )
	{
		foreach ( $teams as $team )
		{
			$this->attachTeam( $team );
		}
	}

	/**
	 * Detach team to this user.
	 *
	 * @param $team
	 *
	 * @return bool
	 */
	public function detachTeam( $team )
	{
		$teamId = $this->retrieveTeamId( $team );
		$detaches = $this->teams()->detach( $teamId );
		return $detaches > 0;
	}

	/**
	 * Detach teams to this user.
	 *
	 * @param $teams
	 */
	public function detachTeams( $teams )
	{
		foreach ( $teams as $team )
		{
			$this->detachTeam( $team );
		}
	}

}