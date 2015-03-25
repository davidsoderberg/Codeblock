<?php

use App\Repositories\Rate\EloquentRateRepository;

class RateTest extends TestCase {

	public $repo;

	public function setUp()
	{
		parent::setUp();
		$this->setUpDb();
		$this->repo = new EloquentRateRepository();
	}

	public function testRate(){
		$this->assertEquals($this->repo->calc(1), 0);
		$this->be(App\User::find(1));
		$this->assertTrue($this->repo->rate(1, '+'));
		$this->assertEquals($this->repo->calc(1), 1);
		$this->assertTrue($this->repo->rate(1, '-'));
		$this->assertEquals($this->repo->calc(1), 0);
		$this->assertTrue($this->repo->rate(1, '-'));
		$this->assertEquals($this->repo->calc(1), -1);
		$this->assertEquals($this->repo->check(1), '-');
		$this->assertTrue($this->repo->rate(1, '+'));
	}

}