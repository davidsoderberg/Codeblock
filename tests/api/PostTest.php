<?php namespace api;

use App\Post;

class PostTest extends \ApiCase {

	public function test_get() {
		$this->get('/api/posts')->seeStatusCode(200);
	}

	/*
	 * Needs token.
	 */
	public function test_create() {
		$this->post('/api/posts', ['name' => 'testar', 'category' => 1, 'description' => 'testar', 'code' => 'testar', 'private' => 1], $this->get_headers())->seeStatusCode(201);
	}

	/*
	 * Needs token.
	 */
	public function test_update() {
		$post = $this->create(Post::class, ['name' => 'testar', 'user_id' => 1]);
		$this->post('/api/posts/'.$post->id, ['_method' => 'put', 'name' => 'testa'], $this->get_headers())->seeStatusCode(201);
	}

	/*
	 * Needs token.
	 */
	public function test_delete() {
		$post = $this->create(Post::class, ['user_id' => 1]);
		$this->post('/api/posts/'.$post->id, ['_method' => 'delete'], $this->get_headers())->seeStatusCode(200);
	}

	/*
	 * Needs token.
	 */
	public function test_star() {
		$post = $this->create(Post::class, ['user_id' => 2]);
		$this->post('/api/posts/star/'.$post->id, [], $this->get_headers())->seeStatusCode(201);
	}

}