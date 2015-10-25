<?php

use App\Repositories\Star\EloquentStarRepository;

class CommentTest extends UnitCase {

	public $repo;

	public function setUp()
	{
		parent::setUp();
		$this->setUpDb();
		$this->repo = new EloquentStarRepository();
	}

	public function testGetComment(){
		$this->assertTrue(count($this->repo->get()) == 0);

		$input = ['post_id' => 1, 'user_id' => 1];
		\App\Star::create($input);

		$this->assertTrue(count($this->repo->get()) == 1);
	}

}