<?php

use Illuminate\Support\Facades\Mail;

class TestCase extends Illuminate\Foundation\Testing\TestCase {

	public function setUp(){
		parent::setUp();
		$this->resetEvents();
	}

	/**
	 * Creates the application.
	 *
	 * @return \Illuminate\Foundation\Application
	 */
	public function createApplication()
	{
		$app = require __DIR__ . '/../bootstrap/app.php';

		$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

		return $app;
	}

	// Hittad på: https://github.com/laravel/framework/issues/1181
	private function resetEvents()
	{
		$models = array('App\Post', 'App\Category', 'App\Tag', 'App\User');
		foreach ($models as $model) {
			call_user_func(array($model, 'flushEventListeners'));
			call_user_func(array($model, 'boot'));
		}
	}

	public function mock($class)
	{
		$mock = Mockery::mock($class);
		$this->app->instance($class, $mock);
		return $mock;
	}

	public function setUpDb($seed = true)
	{
		Artisan::call('migrate');
		Mail::pretend(true);
		if($seed){
			Artisan::call('db:seed');
		}
	}

	public function tearDown()
	{
		Mockery::close();
	}

}
