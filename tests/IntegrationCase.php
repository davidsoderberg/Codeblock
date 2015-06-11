<?php

use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use \Illuminate\Support\Facades\Auth;

class IntegrationCase extends TestCase {

	use TestTrait, DatabaseTransactions;

	protected $baseUrl = 'http://localhost';

	public function tearDown(){
		if(Auth::check()){
			Auth::logout();
		}
	}

	protected $user = ['loginUsername' => 'david', 'loginpassword' => 'test'];

	public function removeField(array $data, $fields){
		if(!is_array($fields)){
			$fields = array($fields);
		}

		foreach($fields as $field){
			unset($data[$field]);
		}
		return $data;
	}

	public function create($model, array $overrides = [], $numbers = 1){
		return factory($model)->times($numbers)->create($overrides);
	}

	protected function sign_in(){
		$this->visit('login')
			->submitForm('Login', $this->user)
			->see('You have logged in.')
			->onPage('/user');
	}

}