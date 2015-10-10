<?php namespace App\ModelTraits;

use App\Team;

trait UserHasTeamsTrait
{

	public function teams()
	{
		return $this->belongsToMany( 'App\Team','team_user', 'user_id', 'team_id' );
	}

	public function ownedTeams()
	{
		return $this->teams()->where( "owner_id", "=", $this->getKey() );
	}

	public function invites()
	{
		return $this->hasMany( 'App\TeamInvite', 'email', 'email' );
	}

	public static function bootUserHasTeams()
	{
		static::deleting( function ( Model $user )
		{
			$user->teams()->sync( [ ] );
			return true;
		} );
	}

	public function isOwner()
	{
		return ( $this->teams()->where( "owner_id", "=", $this->getKey() )->first() ) ? true : false;
	}

	public function isTeamOwner()
	{
		return $this->isOwner();
	}

	public function attachTeam( $team )
	{
		$teamId = $this->retrieveTeamId( $team );
		if( !$this->teams->contains( $team ) )
		{
			$this->teams()->attach( $teamId );
		}
		return $this;
	}

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

	public function isOwnerOfTeam( $team )
	{
		$teamId        = $this->retrieveTeamId( $team );
		$teamKeyName = ( new Team() )->getKeyName();
		return ( ( new Team() )
			->where( "owner_id", "=", $this->getKey() )
			->where( $teamKeyName, "=", $teamId )->first()
		) ? true : false;
	}

	public function attachTeams( $teams )
	{
		foreach ( $teams as $team )
		{
			$this->attachTeam( $team );
		}
	}

	public function detachTeam( $team )
	{
		$teamId = $this->retrieveTeamId( $team );
		$this->teams()->detach( $teamId );
	}

	public function detachTeams( $teams )
	{
		foreach ( $teams as $team )
		{
			$this->detachTeam( $team );
		}
	}

}