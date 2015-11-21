<?php namespace api;

use App\Models\Team;
use App\Models\TeamInvite;
use App\Models\User;

class TeamTest extends \ApiCase {

	public function test_get() {
		$this->get('/api/v1/teams', $this->get_headers())->seeStatusCode(200);

		$team = $this->create(Team::class, ['owner_id' => 1]);
		$this->get('/api/v1/teams/' . $team->id, $this->get_headers())->seeStatusCode(200);
	}

	public function test_create() {
		$this->post('/api/v1/teams', ['name' => 'testare'], $this->get_headers())->seeStatusCode(201);
	}

	public function test_invite() {
		$team = $this->create(Team::class, ['owner_id' => 1]);
		$user = User::find(2);
		$this->post('/api/v1/teams/invite', ['id' => $team->id, 'email' => $user->email], $this->get_headers())
		     ->seeStatusCode(201);

		$team = $this->create(Team::class, ['owner_id' => 1]);
		$user = User::find(1);
		$this->post('/api/v1/teams/invite', ['id' => $team->id, 'email' => $user->email], $this->get_headers())
		     ->seeStatusCode(400);
	}

	public function create_invite() {
		$team = $this->create(Team::class, ['owner_id' => 1]);
		$user = User::find(2);
		$this->post('/api/v1/teams/invite', ['id' => $team->id, 'email' => $user->email], $this->get_headers());
	}

	public function test_respondInviteAccept() {
		$this->create_invite();
		$invite = TeamInvite::find(1);
		$this->post('/api/v1/teams/' . $invite->accept_token, [], $this->get_headers())->seeStatusCode(200);
	}

	public function test_respondInviteDeny() {
		$this->create_invite();
		$invite = TeamInvite::find(1);
		$this->post('/api/v1/teams/' . $invite->deny_token, [], $this->get_headers())->seeStatusCode(200);
	}

	public function join_team($owner_id = 1, $user_id = 2) {
		$team = $this->create(Team::class, ['owner_id' => $owner_id]);
		$user = User::find($user_id);
		$this->post('/api/v1/teams/invite', ['id' => $team->id, 'email' => $user->email], $this->get_headers())
		     ->seeStatusCode(201);
		$this->setUser(2);
		$invite = TeamInvite::find(1);
		$this->post('/api/v1/teams/' . $invite->accept_token, [], $this->get_headers())->seeStatusCode(200);

		return $team;
	}

	public function test_leave() {
		$team = $this->join_team();
		$this->post('/api/v1/teams/leave/' . $team->id, [], $this->get_headers())->seeStatusCode(200);

		$this->post('/api/v1/teams/leave/10', [], $this->get_headers())->seeStatusCode(400);
	}

	public function test_update() {
		$team = $this->create(Team::class, ['owner_id' => 1]);
		$this->post('/api/v1/teams/' . $team->id, ['name' => 'testa', '_method' => 'put'], $this->get_headers())
		     ->seeStatusCode(201);
	}

	public function test_delete() {
		$team = $this->create(Team::class, ['owner_id' => 1]);
		$this->post('/api/v1/teams/' . $team->id, ['_method' => 'delete'], $this->get_headers())->seeStatusCode(200);
	}

}