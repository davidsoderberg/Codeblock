<?php namespace integration;

use Illuminate\Support\Facades\Auth;

class NotificationControllerTest extends \IntegrationCase {

	public function setUp() {
		parent::setUp();
		$this->setUpDb();
		Auth::loginUsingId(1);
	}

	public function create_notification(){
		$this->create('App\Notification', ['user_id' => 1]);
	}

	public function test_notification_view(){
		$this->create_notification();
		$this->visit('notifications')->statusCode(200);
	}

	public function test_delete_notification(){
		$this->create_notification();
		$this->visit('notifications')
			->click('Delete')
			->see('Your notification has been deleted.');
	}

	public function test_delete_none_existing_notification(){
		$this->visit('notifications/delete/1')
			->see('You can not delete that notification.');

		$this->create('App\Notification', ['user_id' => 2]);
		$this->visit('notifications/delete/1')
			->see('You can not delete that notification.');
	}
}