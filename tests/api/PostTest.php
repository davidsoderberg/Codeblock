<?php namespace api;

use App\Models\Post;

class PostTest extends \ApiCase {

	public function test_get() {
		$this->get('/api/v1/posts')->seeStatusCode(200);
	}

	/*
	 * Needs token.
	 */
	public function test_create() {
		$this->post('/api/v1/posts', ['name' => 'testar', 'cat_id' => 1, 'description' => 'testar', 'code' => 'testar', 'private' => 1], $this->get_headers())->seeStatusCode(201);
	}

	/*
	 * Needs token.
	 */
	public function test_update() {
		$post = $this->create(Post::class, ['name' => 'testar', 'user_id' => 1]);
		$this->post('/api/v1/posts/'.$post->id, ['_method' => 'put', 'name' => 'testa'], $this->get_headers())->seeStatusCode(201);
        /*
		$this->setUser(2);
		$post = $this->create(Post::class, ['name' => 'testar', 'user_id' => 2]);
		$this->post('/api/v1/posts/'.$post->id, ['_method' => 'put', 'name' => 'testa2'], $this->get_headers())->seeStatusCode(201);
        */
	}

	/*
	 * Needs token.
	 */
	public function test_delete() {
		$post = $this->create(Post::class, ['user_id' => 1]);
		$this->post('/api/v1/posts/'.$post->id, ['_method' => 'delete'], $this->get_headers())->seeStatusCode(200);

		$this->setUser(2);
		$post = $this->create(Post::class, ['user_id' => 2]);
		$this->post('/api/v1/posts/'.$post->id, ['_method' => 'delete'], $this->get_headers())->seeStatusCode(200);
	}

	/*
	 * Needs token.
	 */
	public function test_star() {
		$post = $this->create(Post::class, ['user_id' => 2]);
		$this->post('/api/v1/posts/star/'.$post->id, [], $this->get_headers())->seeStatusCode(201);
	}

}