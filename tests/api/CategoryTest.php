<?php namespace api;

class CategoryTest extends \ApiCase {

	public function test_get() {
		$this->get('/api/categories')->seeStatusCode(200);
	}

	/*
	 * Needs token.
	 */
	public function test_create() {
		$this->post('/api/categories', ['name' => 'testare'], $this->get_headers())->seeStatusCode(201);
	}

	/*
	 * Needs token.
	 */
	public function test_update() {
		$this->post('/api/categories/12', ['name' => 'testa', '_method' => 'put'], $this->get_headers())->seeStatusCode(201);
	}

	/*
	 * Needs token.
	 */
	public function test_delete() {
		$this->post('/api/categories/1', ['_method' => 'delete'], $this->get_headers())->seeStatusCode(200);
	}

}