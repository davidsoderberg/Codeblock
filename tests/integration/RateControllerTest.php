<?php namespace integration;

use Illuminate\Support\Facades\Auth;

class RateControllerTest extends \IntegrationCase {

	public function setUp() {
		parent::setUp();
		$this->setUpDb();
		Auth::loginUsingId(1);
	}

	public function create_comment(){
		$this->create('App\Models\Post', ['user_id' => 1]);
		$this->create('App\Models\Comment', ['user_id' => 2, 'post_id' => 1]);
	}


	public function test_plus_rate(){
		$this->create_comment();
		$this->visit('/')->visit('rate/plus/1')
			->see('You have now + rated a comment.');
	}

	public function test_plus_rate_none_existing(){
		$this->visit('/')->visit('rate/plus/5')
			->see('You could not rate that comment, please try agian.');
	}

	public function test_minus_rate(){
		$this->create_comment();
		$this->visit('/')->visit('rate/minus/1')
			->see('You have now - rated a comment.');
	}

	public function test_minus_rate_none_existing(){
		$this->visit('/')->visit('rate/minus/1')
			->see('You could not rate that comment, please try agian.');
	}

}