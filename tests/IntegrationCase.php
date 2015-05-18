<?php

use Laracasts\Integrated\Extensions\Laravel as IntegrationTest;
use Laracasts\Integrated\Services\Laravel\DatabaseTransactions;
use Laracasts\TestDummy\Factory;
use \Illuminate\Support\Facades\Auth;

class IntegrationCase extends IntegrationTest {

	use TestTrait, DatabaseTransactions;

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
		Factory::times($numbers)->create($model, $overrides);
	}

	public function getAttributes($model){
		return Factory::attributesFor($model);
	}

	public function Build($model, array $override = []){
		Factory::build($model, $override);
	}

	protected function sign_in(){
		$this->visit('login')
			->submitForm('Login', $this->user)
			->see('You have logged in.')
			->onPage('/user');
	}

}