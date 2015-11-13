<?php namespace api;

class CategoryTest extends \ApiCase {

	public function test_get() {
		$this->get('/api/v1/categories')->seeStatusCode(200);
	}

	/*
	 * Needs token.
	 */
	public function test_create() {
		$this->post('/api/v1/categories', ['name' => 'testare'], $this->get_headers())->seeStatusCode(201);
	}

	/*
	 * Needs token.
	 */
	public function test_update() {
		$this->post('/api/v1/categories/12', ['name' => 'testa', '_method' => 'put'], $this->get_headers())->seeStatusCode(201);
	}

	/*
	 * Needs token.
	 */
	public function test_delete() {
		$this->post('/api/v1/categories/1', ['_method' => 'delete'], $this->get_headers())->seeStatusCode(200);
	}

}