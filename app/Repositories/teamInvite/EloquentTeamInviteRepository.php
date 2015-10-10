<?php namespace App\Repositories\TeamInvite;

use App\Repositories\CRepository;
use App\Team;
use App\TeamInvite;
use App\User;
use Illuminate\Support\Facades\Auth;

class EloquentTeamInviteRepository extends CRepository implements TeamInviteRepository {

	const INVITE = 'invite';
	const REQUEST = 'request';

	public function inviteToTeam( User $user, Team $team, $type = TeamInviteRepository::INVITE ) {

		$invite = new TeamInvite();
		$invite->user_id = Auth::user()->getKey();
		$invite->team_id = $team->id;
		$invite->type = $type;
		$invite->email = $user->email;
		$invite->accept_token = md5( uniqid( microtime() ) );
		$invite->deny_token = md5( uniqid( microtime() ) );

		if ( $invite->save() ) {
			$data = array( 'subject' => 'Team Invite' );
			$emailInfo = array(
				'toEmail' => $user->email,
				'toName'  => $user->username,
				'subject' => $data['subject'],
			);
			return $this->sendEmail( '', $emailInfo, $data );
		} else {
			$this->errors = $invite::$errors;
		}
		return false;
	}

	public function hasPendingInvite( $email, Team $team ) {
		return TeamInvite::where( 'email', "=", $email )
		                 ->where( 'team_id', "=", $team->getKey() )
		                 ->first() ? true : false;
	}

	public function getInviteFromAcceptToken( $token ) {
		return TeamInvite::where( 'accept_token', '=', $token )->first();
	}

	public function acceptInvite( TeamInvite $invite ) {
		Auth::user()->attachTeam( $invite->team );
		$this->deleteInvite($invite);
	}

	public function getInviteFromDenyToken( $token ) {
		return TeamInvite::where( 'deny_token', '=', $token )->first();
	}

	public function denyInvite( TeamInvite $invite ) {
		$this->deleteInvite($invite);
	}

	public function deleteInvite( TeamInvite $invite ){
		return $invite->delete();
	}

}