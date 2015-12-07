<?php namespace api;

class ArticleTest extends \ApiCase {

	public function test_get(){
		$this->get('/api/v1/articles')->seeStatusCode(200);
	}

}