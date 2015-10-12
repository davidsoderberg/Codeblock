<?php namespace App\Http\Controllers;

use App\Repositories\User\UserRepository;
use App\TeamInvite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use App\Repositories\TeamInvite\TeamInviteRepository;
use App\Repositories\Team\TeamRepository;

/**
 * Class TeamController
 *
 * @package App\Http\Controllers
 */
class TeamController extends Controller {

	/**
	 * @var TeamRepository
	 */
	private $teamRepository;
	/**
	 * @var TeamInviteRepository
	 */
	private $inviteRepository;

	/**
	 * @param TeamRepository       $team
	 * @param TeamInviteRepository $invite
	 */
	public function __construct( TeamRepository $team, TeamInviteRepository $invite ) {
		parent::__construct();
		$this->teamRepository   = $team;
		$this->inviteRepository = $invite;

	}

	/**
	 * @param null $id
	 *
	 * @permission view_teams
	 * @return mixed
	 */
	public function index( $id = null ) {
		$team = null;

		if ( is_numeric( $id ) ) {
			$team = $this->teamRepository->get( $id );
		}

		return View::make( 'team.index' )
		           ->with( 'title', 'Teams' )
		           ->with( 'teams', $this->teamRepository->get() )
		           ->with( 'team', $team );
	}

	/**
	 * @param $id
	 *
	 * @return mixed
	 */
	private function show( $id ) {
		$team = $this->teamRepository->get( $id );

		return View::make( 'team.show' )->with( 'title', 'Team: ' . $team->title )->with( 'team', $team );
	}

	/**
	 * @return mixed
	 */
	public function listTeams( $id = null ) {
		if ( is_numeric( $id ) ) {
			return $this->show( $id );
		}

		return View::make( 'team.list' )->with( 'title', 'Teams for: ' . Auth::user()->username );
	}

	/**
	 * @param null $id
	 *
	 * @permission create_team:optional
	 * @return mixed
	 */
	public function createOrUpdate( $id = null ) {
		if ( $this->teamRepository->createOrUpdate( $this->request->all(), $id ) ) {
			if ( is_null( $id ) ) {
				return Redirect::to( 'teams' )->with( 'success', 'Your team has been created.' );
			}

			return Redirect::to( 'teams' )->with( 'success', 'Your team has been updated.' );
		}

		return Redirect::back()->withErrors( $this->teamRepository->getErrors() )->withInput( $this->request->all() );
	}

	/**
	 * @param UserRepository $user
	 * @param                $id
	 *
	 * @return mixed
	 */
	public function invite( UserRepository $user, $id ) {
		$userId = $user->getIdByEmail( $this->request->get( 'email' ) );
		$team   = $this->teamRepository->get( $id );
		$user   = $user->get( $userId );

		if ( $this->inviteRepository->inviteToTeam( $user, $team ) ) {
			return Redirect::back()
			               ->with( 'success', 'You have invite ' . $user->username . ' to ' . $team->title . '.' );
		}

		return Redirect::back()
		               ->with( 'errors', 'You could not invite ' . $user->username . ' to ' . $team->title . '.' )
		               ->withInput( $this->request->all() );
	}

	/**
	 * @return mixed
	 */
	public function respondInvite() {
		$token  = $this->request->get( 'token' );
		$action = 'accepted';

		$invite = $this->inviteRepository->getInviteFromAcceptToken( $token );
		if ( ! $invite instanceof TeamInvite ) {
			$invite = $this->inviteRepository->getInviteFromDenyToken( $token );
			$action = 'denied';
		}

		if ( $action == 'accepted' ) {
			$inviteResponded = $this->inviteRepository->acceptInvite( $invite );
		} else {
			$inviteResponded = $this->inviteRepository->denyInvite( $invite );
		}

		if ( $inviteResponded ) {
			return Redirect::back()->with( 'success', 'You have now ' . $action . ' that invite.' );
		}

		return Redirect::back()->with( 'errors', 'That invite could not be ' . $action . '.' );
	}

	/**
	 * @param $id
	 *
	 * @permission delete_team:optional
	 * @return mixed
	 */
	public function delete( $id ) {
		if ( $this->teamRepository->delete( $id ) ) {
			return Redirect::back()->with( 'success', 'Your team has been deleted.' );
		}

		return Redirect::back();
	}
}