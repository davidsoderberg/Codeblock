<?php

use Laracasts\Integrated\Extensions\Laravel as IntegrationTest;
use Laracasts\Integrated\Services\Laravel\DatabaseTransactions;

class IntegrationCase extends IntegrationTest {

	use TestTrait, DatabaseTransactions;

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

	protected function sign_in(){
		$this->visit('login')
			->submitForm('Login', $this->user)
			->see('You have logged in.')
			->onPage('/user');
	}

}