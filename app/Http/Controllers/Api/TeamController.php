<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Exceptions\NullPointerException;
use App\Repositories\Team\TeamRepository;
use App\Repositories\TeamInvite\TeamInviteRepository;
use App\Repositories\User\UserRepository;
use Illuminate\Support\Facades\Auth;

/**
 * Class TeamController
 * @package App\Http\Controllers\Api
 */
class TeamController extends ApiController
{

    /**
     * Fetch one or all teams.
     *
     * @param TeamRepository $teamRepository
     * @param null $id
     *
     * @return mixed
     */
    public function teams(TeamRepository $teamRepository, $id = null)
    {
        if (is_null($id)) {
            $teams = Auth::user()->teams->merge(Auth::user()->ownedTeams);
        } else {
            $teams = $this->getCollection($teamRepository, $id);
        }

        return $this->response([$this->stringData => $teams], 200);
    }

    /**
     * Creates or updates a team.
     *
     * @param TeamRepository $teamRepository
     * @param null $id
     *
     * @return mixed
     */
    public function createOrUpdateTeam(TeamRepository $teamRepository, $id = null)
    {
        if ($teamRepository->createOrUpdate($this->request->all(), $id)) {
            if (is_null($id)) {
                return $this->response([$this->stringMessage => 'Your team has been created.'], 201);
            }

            return $this->response([$this->stringMessage => 'Your team has been updated.'], 201);
        }

        return $this->response([$this->stringErrors => $teamRepository->getErrors()], 400);
    }

    /**
     * Invites a user to team.
     *
     * @param TeamRepository $teamRepository
     * @param TeamInviteRepository $teamInviteRepository
     * @param UserRepository $user
     *
     * @return mixed
     */
    public function invite(
        TeamRepository $teamRepository,
        TeamInviteRepository $teamInviteRepository,
        UserRepository $user
    ) {
        $team = $teamRepository->get($this->request->get('id'));
        $user = $user->get($user->getIdByEmail($this->request->get('email')));

        if ($user->id === $team->owner_id) {
            return $this->response([$this->stringErrors => 'You can not invite yourself to your own team.'], 400);
        }

        if ($teamInviteRepository->inviteToTeam($user, $team)) {
            return $this->response([$this->stringMessage => 'You have invite ' . $user->username . ' to ' . $team->name . '.'],
                201);
        }

        return $this->response([$this->stringErrors => 'You could not invite ' . $user->username . ' to ' . $team->name . '.'],
            400);
    }

    /**
     * Make user leave a team.
     *
     * @param TeamRepository $teamRepository
     * @param $id
     *
     * @return mixed
     */
    public function leave(TeamRepository $teamRepository, $id)
    {
        if ($teamRepository->leave($id)) {
            return $this->response([$this->stringMessage => 'You have leaved that team now.'], 200);
        }

        return $this->response([$this->stringErrors => 'You could not leave that team.'], 400);
    }

    /**
     * Respondes invite for user.
     *
     * @param TeamInviteRepository $teamInviteRepository
     * @param UserRepository $userRepository
     * @param $token
     *
     * @return mixed
     */
    public function respondInvite(TeamInviteRepository $teamInviteRepository, UserRepository $userRepository, $token)
    {
        try {
            $action = '';
            if ($teamInviteRepository->respondInvite($userRepository, $token, $action)) {
                return $this->response([$this->stringMessage => 'You have now ' . $action . ' that invite.'], 200);
            }

            return $this->response([$this->stringErrors => 'That invite could not be ' . $action . '.'], 400);
        } catch (NullPointerException $e) {
            return $this->response([$this->stringErrors => 'That invite are invalid.'], 400);
        }
    }

    /**
     * Deletes a team.
     *
     * @param TeamRepository $teamRepository
     * @param $id
     *
     * @permission delete_team:optional
     * @return mixed
     */
    public function deleteTeam(TeamRepository $teamRepository, $id)
    {
        if ($teamRepository->delete($id)) {
            return $this->response([$this->stringMessage => 'Your team has been deleted.'], 200);
        }

        return $this->response([$this->stringErrors => 'Your team could not be deleted.'], 204);
    }
}
