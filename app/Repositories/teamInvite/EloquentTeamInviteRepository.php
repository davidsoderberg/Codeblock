<?php namespace App\Repositories\TeamInvite;

use App\Exceptions\NullPointerException;
use App\Repositories\CRepository;
use App\Repositories\User\UserRepository;
use App\Team;
use App\TeamInvite;
use App\User;
use Illuminate\Support\Facades\Auth;

class EloquentTeamInviteRepository extends CRepository implements TeamInviteRepository {

	const INVITE = 'invite';
	const REQUEST = 'request';

	public function inviteToTeam(User $user, Team $team, $type = Self::INVITE) {

		$invite = new TeamInvite();
		$invite->user_id = Auth::user()->getKey();
		$invite->team_id = $team->id;
		$invite->type = $type;
		$invite->email = $user->email;
		$invite->accept_token = md5(uniqid(microtime()));
		$invite->deny_token = md5(uniqid(microtime()));

		try {
			if($invite->save()) {
				$data = array(
					'subject' => 'Team Invite',
					'invite' => $invite,
					'user' => $user,
					'team' => $team
				);
				$emailInfo = array(
					'toEmail' => $user->email,
					'toName' => $user->username,
					'subject' => $data['subject'],
				);
				if(!$this->sendEmail('emails.invite', $emailInfo, $data)) {
					$this->deleteInvite($invite);
				} else {
					return true;
				}
			} else {
				$this->errors = $invite::$errors;
			}
		} catch(\PDOException $e){}

		return false;
	}

	public function hasPendingInvite($email, Team $team) {
		return TeamInvite::where('email', "=", $email)->where('team_id', "=", $team->getKey())->first() ? true : false;
	}

	public function getInviteFromAcceptToken($token) {
		return TeamInvite::where('accept_token', '=', $token)->first();
	}

	public function acceptInvite(TeamInvite $invite, User $user = null) {
		if(is_null($user)){
			$user = Auth::user();
		}
		$user->attachTeam($invite->team);
		return $this->deleteInvite($invite);
	}

	public function getInviteFromDenyToken($token) {
		return TeamInvite::where('deny_token', '=', $token)->first();
	}

	public function denyInvite(TeamInvite $invite) {
		return $this->deleteInvite($invite);
	}

	public function respondInvite(UserRepository $userRepository, $token, &$action){

		$action = 'accepted';
		$invite = $this->getInviteFromAcceptToken( $token );
		if ( ! $invite instanceof TeamInvite ) {
			$invite = $this->getInviteFromDenyToken( $token );
			$action = 'denied';
		}

		if(is_null($invite)){
			throw new NullPointerException('Invalid invite.');
			return;
		}

		if ( $action == 'accepted' ) {
			$user = null;

			if(!Auth::check()){
				$user = $userRepository->getIdByEmail($invite->email);
				$user = $userRepository->get($user);
			}

			$inviteResponded = $this->acceptInvite( $invite, $user );
		} else {
			$inviteResponded = $this->denyInvite( $invite );
		}

		return $inviteResponded;

	}

	private function deleteInvite(TeamInvite $invite) {
		$delete = $invite->delete();
		if(is_null($delete)){
			return false;
		}
		return $delete;
	}

}