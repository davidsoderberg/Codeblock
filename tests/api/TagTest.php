<?php namespace api;

class TagTest extends \ApiCase {

	public function test_get() {
		$this->get('/api/v1/tags')->seeStatusCode(200);
	}

	/*
	 * Needs token.
	 */
	public function test_create() {
		$this->post('/api/v1/tags', ['name' => 'testare'], $this->get_headers())->seeStatusCode(201);
	}

	/*
	 * Needs token.
	 */
	public function test_update() {
		$this->post('/api/v1/tags/12', ['name' => 'testa', '_method' => 'put'], $this->get_headers())->seeStatusCode(201);
	}

	/*
	 * Needs token.
	 */
	public function test_delete() {
		$this->post('/api/v1/tags/1', ['_method' => 'delete'], $this->get_headers())->seeStatusCode(200);
	}

}