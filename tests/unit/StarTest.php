<?php

use App\Repositories\Star\EloquentStarRepository;

class StarTest extends UnitCase {

	public $repo;

	public function setUp()
	{
		parent::setUp();
		$this->setUpDb();
		$this->repo = new EloquentStarRepository();
	}

	public function testGetStars(){
		$this->assertTrue(count($this->repo->get()) == 0);

		$input = ['post_id' => 1, 'user_id' => 1];
		\App\Models\Star::create($input);

		$this->assertTrue(count($this->repo->get()) == 1);
	}

}