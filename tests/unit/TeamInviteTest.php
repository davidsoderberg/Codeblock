<?php

use App\Repositories\TeamInvite\EloquentTeamInviteRepository;
use App\Repositories\Team\EloquentTeamRepository;
use App\Models\User;
use App\Models\Team;
use App\Models\TeamInvite;

class TeamInviteTest extends UnitCase {

	public $repo;

	private $team;

	protected $user;

	public function setUp()
	{
		parent::setUp();
		$this->setUpDb();
		$this->repo = new EloquentTeamInviteRepository();
		$this->user = User::find(1);
		$this->be($this->user);
		$this->createTeamDummy();
	}

	public function createTeamDummy(){
		$repo = new EloquentTeamRepository();
		$input = ['name' => 'test'];
		$repo->createOrUpdate($input);

		$this->team = $repo->get(1);
	}

	public function createDummy(){
		$this->repo->inviteToTeam($this->user, $this->team);
	}

	public function testInviteToTeam(){
		$this->assertTrue($this->repo->inviteToTeam($this->user, $this->team));
		$this->assertFalse($this->repo->inviteToTeam(new User(), new Team()));
		$this->assertFalse($this->repo->inviteToTeam($this->user, new Team()));
		$this->assertFalse($this->repo->inviteToTeam(new User(), $this->team));
	}

	public function testHasPendingInvite(){
		$this->assertFalse($this->repo->hasPendingInvite($this->user->email, $this->team));
		$this->createDummy();
		$this->assertTrue($this->repo->hasPendingInvite($this->user->email, $this->team));
	}

	public function testAcceptInvite(){
		$this->assertFalse($this->repo->acceptInvite(new TeamInvite()));
		$this->createDummy();
		$invite = TeamInvite::find(1);
		$this->assertTrue($this->repo->acceptInvite($this->repo->getInviteFromAcceptToken($invite->accept_token)));
	}

	public function testDenyInvite(){
		$this->assertFalse($this->repo->denyInvite(new TeamInvite()));
		$this->createDummy();
		$invite = TeamInvite::find(1);
		$this->assertTrue($this->repo->denyInvite($this->repo->getInviteFromDenyToken($invite->deny_token)));
	}

	public function testRespondInviteAccept(){
		$repo = new \App\Repositories\User\EloquentUserRepository();
		$this->createDummy();
		$invite = TeamInvite::find(1);
		$action = '';
		$this->assertTrue($this->repo->respondInvite($repo, $invite->accept_token,$action));
		$this->assertEquals('accepted', $action);
	}

	public function testRespondInviteDeny(){
		$repo = new \App\Repositories\User\EloquentUserRepository();
		$this->createDummy();
		$invite = TeamInvite::find(1);
		$action = '';
		$this->assertTrue($this->repo->respondInvite($repo, $invite->deny_token,$action));
		$this->assertEquals('denied', $action);
	}

	public function testRspondInviteException(){
		$repo = new \App\Repositories\User\EloquentUserRepository();
		$action = '';
		$this->setExpectedException('App\Exceptions\NullPointerException');
		$this->repo->respondInvite($repo, '',$action);
	}

}