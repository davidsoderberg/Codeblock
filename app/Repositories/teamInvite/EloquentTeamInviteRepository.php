<?php namespace App\Repositories\TeamInvite;

use App\Exceptions\NullPointerException;
use App\Repositories\CRepository;
use App\Repositories\User\UserRepository;
use App\Models\Team;
use App\Models\TeamInvite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

/**
 * Class EloquentTeamInviteRepository
 * @package App\Repositories\TeamInvite
 */
class EloquentTeamInviteRepository extends CRepository implements TeamInviteRepository
{

    /**
     * Constant for string invite.
     */
    const INVITE = 'invite';
    /**
     * Constant for string request.
     */
    const REQUEST = 'request';

    /**
     * Invites user to team.
     *
     * @param \App\Models\User $user
     * @param Team $team
     * @param string $type
     *
     * @return bool
     */
    public function inviteToTeam(User $user, Team $team, $type = Self::INVITE)
    {
        $invite = new TeamInvite();
        $invite->user_id = Auth::user()->getKey();
        $invite->team_id = $team->id;
        $invite->type = $type;
        $invite->email = $user->email;
        $invite->accept_token = md5(uniqid(microtime()));
        $invite->deny_token = md5(uniqid(microtime()));

        try {
            if ($invite->save()) {
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
                if (!$this->sendEmail('emails.invite', $emailInfo, $data)) {
                    $this->deleteInvite($invite);
                } else {
                    return true;
                }
            } else {
                $this->errors = $invite::$errors;
            }
        } catch (\PDOException $e) {
        }

        return false;
    }

    /**
     * Checks if user has pending invite.
     *
     * @param $email
     * @param Team $team
     *
     * @return bool
     */
    public function hasPendingInvite($email, Team $team)
    {
        return TeamInvite::where('email', "=", $email)->where('team_id', "=", $team->getKey())->first() ? true : false;
    }

    /**
     * Fetch invite from accept token.
     *
     * @param $token
     *
     * @return mixed
     */
    public function getInviteFromAcceptToken($token)
    {
        return TeamInvite::where('accept_token', '=', $token)->first();
    }

    /**
     * Accept invite for user.
     *
     * @param TeamInvite $invite
     * @param User|null $user
     *
     * @return bool|null
     */
    public function acceptInvite(TeamInvite $invite, User $user = null)
    {
        if (is_null($user)) {
            $user = Auth::user();
        }
        $user->attachTeam($invite->team);
        return $this->deleteInvite($invite);
    }

    /**
     * Fetch invite from deny token.
     *
     * @param $token
     *
     * @return mixed
     */
    public function getInviteFromDenyToken($token)
    {
        return TeamInvite::where('deny_token', '=', $token)->first();
    }

    /**
     * Deny invite for user.
     *
     * @param TeamInvite $invite
     *
     * @return bool|null
     */
    public function denyInvite(TeamInvite $invite)
    {
        return $this->deleteInvite($invite);
    }

    /**
     * Responds invite for user.
     *
     * @param UserRepository $userRepository
     * @param $token
     * @param $action
     *
     * @return bool|null|void
     * @throws NullPointerException
     */
    public function respondInvite(UserRepository $userRepository, $token, &$action)
    {
        $action = 'accepted';
        $invite = $this->getInviteFromAcceptToken($token);
        if (!$invite instanceof TeamInvite) {
            $invite = $this->getInviteFromDenyToken($token);
            $action = 'denied';
        }

        if (is_null($invite)) {
            throw new NullPointerException('Invalid invite.');
            return;
        }

        if ($action == 'accepted') {
            $user = null;

            if (!Auth::check()) {
                $user = $userRepository->getIdByEmail($invite->email);
                $user = $userRepository->get($user);
            }

            $inviteResponded = $this->acceptInvite($invite, $user);
        } else {
            $inviteResponded = $this->denyInvite($invite);
        }

        return $inviteResponded;
    }

    /**
     * Deletes invite.
     *
     * @param \App\Models\TeamInvite $invite
     *
     * @return bool|null
     * @throws \Exception
     */
    private function deleteInvite(TeamInvite $invite)
    {
        $delete = $invite->delete();
        if (is_null($delete)) {
            return false;
        }
        return $delete;
    }
}
