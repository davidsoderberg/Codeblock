<?php

use App\Repositories\Post\EloquentPostRepository;
use App\Repositories\Comment\EloquentCommentRepository;
use App\User;

class PostTest extends UnitCase {

	public $repo;

	public function setUp()
	{
		parent::setUp();
		$this->setUpDb();
		$this->repo = new EloquentPostRepository();
		$this->repoComment = new EloquentCommentRepository();
	}

	public function testCreateOrUpdate(){
		$input = ['name' => 'test', 'cat_id' => 2, 'description' => 'test', 'code' => 'test'];
		$this->be(User::find(1));
		$this->assertTrue($this->repo->createOrUpdate($input));
		$this->assertfalse($this->repo->createOrUpdate(['name' => ''],1));
		$this->assertTrue(is_object($this->repo->getErrors()));
		$this->assertTrue($this->repo->createOrUpdate(['name' => 'hej'],1));
		$this->repo->createOrUpdate(['name' => 'test'],1);
		$input['name'] = 'tv';
		$this->assertFalse($this->repo->createOrUpdate($input));
		$this->assertTrue(count($this->repo->errors) > 0);

		$this->repo->errors = null;
		$this->assertFalse($this->repo->createOrUpdate(['name' => '', 'cat_id' => 2, 'description' => '', 'code' => ''], 1));
		$this->assertTrue(count($this->repo->errors) > 0);

		$this->repo->errors = null;
		$this->assertFalse($this->repo->createOrUpdate(['name' => ' ', 'cat_id' => 2, 'description' => 'test', 'code' => 'test'], 1));
		$this->assertTrue(count($this->repo->errors) > 0);
	}

	public function testGet(){
		$this->assertTrue(is_object($this->repo->get()));
		$this->assertFalse(is_object($this->repo->get(1)));

		$input = ['name' => 'test', 'cat_id' => 2, 'description' => 'test', 'code' => 'test'];
		$this->be(User::find(1));
		$this->assertTrue($this->repo->createOrUpdate($input));
		$this->assertTrue(is_object($this->repo->get(1)));
	}

	public function testGetByCategory(){
		$input = ['name' => 'test', 'cat_id' => 2, 'description' => 'test', 'code' => 'test'];
		$this->be(User::find(1));
		$this->repo->createOrUpdate($input);
		$this->assertEquals(count($this->repo->getByCategory(2)),1);
	}

	public function testGetByNew(){
		$input = ['name' => 'test', 'cat_id' => 2, 'description' => 'test', 'code' => 'test'];
		$this->be(User::find(1));
		$this->repo->createOrUpdate($input);
		$this->assertEquals(count($this->repo->getByCategory(0)),1);
	}

	public function testGetByTag(){
		$input = ['name' => 'test', 'cat_id' => 2, 'description' => 'test', 'code' => 'test', 'tags' => [1]];
		$this->be(User::find(1));
		$this->repo->createOrUpdate($input);
		$this->assertEquals(count($this->repo->getByTag(1)),1);
	}

	public function testFork(){
		$input = ['name' => 'test', 'cat_id' => 2, 'description' => 'test', 'code' => 'test'];
		$this->be(User::find(1));
		$this->assertTrue($this->repo->createOrUpdate($input));
		$this->assertTrue($this->repo->duplicate(1));
	}

	public function testGetForked()
	{
		$input = ['name' => 'test', 'cat_id' => 2, 'description' => 'test', 'code' => 'test'];
		$this->be(User::find(1));
		$this->assertTrue($this->repo->createOrUpdate($input));
		$this->assertTrue($this->repo->duplicate(1));
		$this->assertEquals(count($this->repo->getForked(1)),1);
		$this->assertEquals(count($this->repo->getForked(2)),0);
	}

	public function testDelete(){
		$this->assertFalse($this->repo->delete(1));
		$input = ['name' => 'test', 'cat_id' => 2, 'description' => 'test', 'code' => 'test'];
		$this->be(User::find(1));
		$this->assertTrue($this->repo->createOrUpdate($input));
		$this->assertTrue($this->repo->delete(1));
	}

	public function testStaring(){
		$input = ['name' => 'test', 'cat_id' => 2, 'description' => 'test', 'code' => 'test'];
		$this->be(User::find(1));
		$this->assertTrue($this->repo->createOrUpdate($input));
		$post = $this->repo->get(1);
		$return = $this->repo->createOrDeleteStar($post->id);
		$this->assertTrue($return[0]);
		$post = $this->repo->get(1);
		$this->assertTrue($post->starcount == 1);
		$return = $this->repo->createOrDeleteStar($post->id);
		$this->assertTrue($return[0]);
		$post = $this->repo->get(1);
		$this->assertTrue($post->starcount == 0);
	}

	public function testGetComment(){
		$this->assertTrue(is_object($this->repoComment->get()));
		$this->assertFalse(is_object($this->repoComment->get(1)));

		$this->be(User::find(1));
		$input = ['name' => 'test', 'cat_id' => 2, 'description' => 'test', 'code' => 'test'];
		$this->repo->createOrUpdate($input);
		$input = ['post_id' => 1, 'comment' => 'hej'];
		$this->assertTrue($this->repoComment->createOrUpdate($input));

		$this->assertTrue(is_object($this->repoComment->get(1)));
	}

	public function testCreateOrUpdateComment(){
		$input = ['name' => 'test', 'cat_id' => 2, 'description' => 'test', 'code' => 'test'];
		$this->be(User::find(1));
		$this->assertTrue($this->repo->createOrUpdate($input));
		$input = ['post_id' => 1, 'comment' => 'test'];
		$this->assertTrue($this->repoComment->createOrUpdate($input));
		$input = ['status' => '2'];
		$this->assertTrue($this->repoComment->createOrUpdate($input, 1));
		$this->assertEquals(count($this->repo->get(1)->comments),1);
	}

	public function testDeleteComment(){
		$input = ['name' => 'test', 'cat_id' => 2, 'description' => 'test', 'code' => 'test'];
		$this->be(User::find(1));
		$this->assertTrue($this->repo->createOrUpdate($input));
		$this->assertFalse($this->repoComment->delete(1));
		$input = ['post_id' => 1, 'comment' => 'hej'];
		$this->assertTrue($this->repoComment->createOrUpdate($input));
		$this->assertTrue($this->repoComment->delete(1));
	}

}