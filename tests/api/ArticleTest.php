<?php namespace api;

class ArticleTest extends \ApiCase {

	public function test_get(){
		$this->get('/api/articles')->seeStatusCode(200);
	}

}