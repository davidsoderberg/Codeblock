<?php namespace App\Http\Controllers;

use App\Exceptions\NullPointerException;
use App\Repositories\User\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Redirect;
use App\Repositories\TeamInvite\TeamInviteRepository;
use App\Repositories\Team\TeamRepository;

/**
 * Class TeamController
 *
 * @package App\Http\Controllers
 */
class TeamController extends Controller
{

    /**
     * Property to store TeamRepository in.
     *
     * @var TeamRepository
     */
    private $teamRepository;

    /**
     * Property to store TeamInviteRepository in.
     *
     * @var TeamInviteRepository
     */
    private $inviteRepository;

    /**
     * Constructor for TeamController.
     *
     * @param TeamRepository $team
     * @param TeamInviteRepository $invite
     */
    public function __construct(TeamRepository $team, TeamInviteRepository $invite)
    {
        parent::__construct();
        $this->teamRepository = $team;
        $this->inviteRepository = $invite;
    }

    /**
     * Render index view for teams.
     *
     * @param null $id
     *
     * @permission view_team
     * @return mixed
     */
    public function index($id = null)
    {
        $team = null;

        if (is_numeric($id)) {
            $team = $this->teamRepository->get($id);
        }

        return View::make('team.index')
            ->with('title', 'Teams')
            ->with('teams', $this->teamRepository->get())
            ->with('team', $team);
    }

    /**
     * Render choosen team.
     *
     * @param $id
     *
     * @return mixed
     */
    private function show($id)
    {
        $team = $this->teamRepository->get($id);

        return View::make('team.show')->with('title', 'Team: ' . $team->name)->with('team', $team);
    }

    /**
     * Render list teams.
     *
     * @param int id
     *
     * @return mixed
     */
    public function listTeams($id = null)
    {
        if (is_numeric($id)) {
            return $this->show($id);
        }

        return View::make('team.list')->with('title', 'Teams for: ' . Auth::user()->username);
    }

    /**
     * Creates or update a team.
     *
     * @param null $id
     *
     * @permission create_team:optional
     * @return mixed
     */
    public function createOrUpdate($id = null)
    {
        if ($this->teamRepository->createOrUpdate($this->request->all(), $id)) {
            if (is_null($id)) {
                return Redirect::back()->with('success', 'Your team has been created.');
            }

            return Redirect::back()->with('success', 'Your team has been updated.');
        }

        return Redirect::back()->withErrors($this->teamRepository->getErrors())->withInput($this->request->all());
    }

    /**
     * Invite user to team.
     *
     * @param UserRepository $user
     *
     * @return mixed
     */
    public function invite(UserRepository $user)
    {
        $team = $this->teamRepository->get($this->request->get('id'));
        $user = $user->get($user->getIdByEmail($this->request->get('email')));

        if ($this->teamRepository->isOwner($user, $team)) {
            return Redirect::back()->with('error', 'You can not invite yourself to your own team.');
        }

        if ($this->inviteRepository->inviteToTeam($user, $team)) {
            return Redirect::back()->with('success', 'You have invite ' . $user->username . ' to ' . $team->name . '.');
        }

        return Redirect::back()
            ->with('error', 'You could not invite ' . $user->username . ' to ' . $team->name . '.')
            ->withInput($this->request->all());
    }

    /**
     * Make user leave team.
     *
     * @param $id
     *
     * @return mixed
     */
    public function leave($id)
    {
        if ($this->teamRepository->leave($id)) {
            return Redirect::back()->with('success', 'You have leaved that team now.');
        }

        return Redirect::back()->with('error', 'You could not leave that team.');
    }

    /**
     * Responds to team invite.
     *
     * @param UserRepository $userRepository
     * @param $token
     *
     * @return mixed
     */
    public function respondInvite(UserRepository $userRepository, $token)
    {
        try {
            $action = '';
            if ($this->inviteRepository->respondInvite($userRepository, $token, $action)) {
                return Redirect::back()->with('success', 'You have now ' . $action . ' that invite.');
            }

            return Redirect::back()->with('error', 'That invite could not be ' . $action . '.');
        } catch (NullPointerException $e) {
            return Redirect::to('/')->with('error', 'That invite are invalid.');
        }
    }

    /**
     * Delets a team.
     *
     * @param $id
     *
     * @permission delete_team:optional
     * @return mixed
     */
    public function delete($id)
    {
        if ($this->teamRepository->delete($id)) {
            return Redirect::back()->with('success', 'Your team has been deleted.');
        }

        return Redirect::back()->with('error', 'Your team could not be deleted.');
    }
}
