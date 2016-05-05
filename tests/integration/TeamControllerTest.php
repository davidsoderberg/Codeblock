<?php namespace integration;

use App\Models\Team;
use App\Models\TeamInvite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Repositories\User\EloquentUserRepository;

class TeamControllerTest extends \IntegrationCase {

	public function setUp() {
		parent::setUp();
		$this->setUpDb();
		Auth::loginUsingId(1);
	}

	public function create_user() {
		$repo = new EloquentUserRepository();
		$input = [
			'username' => 'test',
			'password' => 'test',
			'email' => 'test@test.com',
			'active' => 1,
			'role' => 1,
			'active' => 1
		];

		$repo->createOrUpdate($input);

		return $repo->get($repo->getIdByEmail($input['email']));
	}

	public function create_team() {
		$this->visit('team')->submitForm('Create team', ['name' => 'test'])->see('Your team has been created.');
		$this->flush_flash();

		return $this;
	}

	public function test_create_team() {
		$this->visit('team')->submitForm('Create team', ['name' => 'test'])->see('Your team has been created.');
	}

	public function test_edit_article() {
		$this->create_team();

		$this->visit('team/1')
		     ->submitForm('Update team', ['name' => 'hej'])
		     ->see('hej')
		     ->see('Your team has been updated.')
		     ->seePageIs('team/1');
	}

	public function test_delete_article() {
		$this->create_team();

		$this->visit('teams/delete/1')->see('Your team has been deleted.')->seePageIs('team');
	}

	public function test_delete_none_existing_article() {
		$this->visit('team')->visit('teams/delete/1')->see('Your team could not be deleted.')->seePageIs('team');
	}


	public function test_invite() {
		$user = $this->create_user();

		$this->create_team();
		$this->visit('team/1')
		     ->submitForm('Add member', ['email' => $user->email])
		     ->see($user->username)
		     ->see('You have invite ' . $user->username . ' to test.')
		     ->seePageIs('team/1');
	}

	public function test_invite_owner() {
		$user = User::find(1);
		$this->create_team();

		$this->visit('team')
		     ->visit('team/1')
		     ->submitForm('Add member', ['email' => $user->email])
		     ->see('You can not invite yourself to your own team.')
		     ->seePageIs('team/1');
	}

	public function test_leave() {
		$user = $this->create_user();
		$this->create_team();
		$user->attachTeam(Team::find(1));

		Auth::loginUsingId(3);

		$this->visit('team')->visit('team/leave/1')->see('You have leaved that team now.');
	}

	public function test_leave_not_in() {
		$this->create_team();

		$this->visit('team')->visit('team/leave/1')->see('You could not leave that team.');
	}

	public function test_respondInvite_accept() {
		$this->create_team();
		$user = $this->create_user();

		$this->visit('team/1')
		     ->submitForm('Add member', ['email' => $user->email])
		     ->see('You have invite ' . $user->username . ' to test.');

		$this->flush_flash();

		Auth::logout();

		$invite = TeamInvite::find(1);

		$this->visit('/')->visit('team/' . $invite->accept_token)->see('You have now accepted that invite.');
	}

	public function test_respondInvite_deny() {
		$this->create_team();
		$user = $this->create_user();

		$this->visit('team/1')
		     ->submitForm('Add member', ['email' => $user->email])
		     ->see('You have invite ' . $user->username . ' to test.');

		$this->flush_flash();

		Auth::logout();

		$invite = TeamInvite::find(1);

		$this->visit('/')->visit('team/' . $invite->deny_token)->see('You have now denied that invite.');
	}

	public function test_respondInvite_invalid() {
		$this->flush_flash();
		Auth::logout();

		$this->visit('team/hej')->see('That invite are invalid.');
	}

}