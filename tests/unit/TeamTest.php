<?php

use App\Repositories\Team\EloquentTeamRepository;
use App\Models\User;
use App\Models\Team;

class TeamTest extends UnitCase {

	public $repo;

	public function setUp()
	{
		parent::setUp();
		$this->setUpDb();
		$this->repo = new EloquentTeamRepository();
		$this->be(User::find(1));
	}

	public function createDummy(){
		$input = ['name' => 'test'];
		$this->repo->createOrUpdate($input);
	}

	public function testCreateOrUpdate(){
		$this->assertTrue($this->repo->createOrUpdate(['name' => 'test']));
		$this->assertFalse($this->repo->createOrUpdate(['name' => '']));
		$this->assertTrue(is_object($this->repo->getErrors()));
		$this->repo->errors = null;
		$this->assertfalse($this->repo->createOrUpdate(['name' => ''],1));
		$this->assertTrue(is_object($this->repo->getErrors()));
		$this->assertTrue($this->repo->createOrUpdate(['name' => 'hej'],1));
		$this->repo->createOrUpdate(['name' => 'test'],1);
	}

	public function testGet(){
		$this->assertTrue(is_object($this->repo->get()));
		$this->assertTrue(count($this->repo->get()) == 0);
		$this->assertFalse(is_object($this->repo->get(1)));
		$this->createDummy();
		$this->assertTrue(is_object($this->repo->get(1)));
		$this->assertTrue(count($this->repo->get()) > 0);
	}

	public function testDelete(){
		$this->assertFalse($this->repo->delete(1));
		$this->createDummy();
		$this->assertTrue($this->repo->delete(1));
	}

	public function testLeave(){
		$this->assertFalse($this->repo->leave(1));
		$this->createDummy();
		$team = Team::find(1);
		User::find(1)->attachTeam($team);
		$this->assertTrue($this->repo->leave($team));
	}

}