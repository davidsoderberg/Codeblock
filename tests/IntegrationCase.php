<?php // tests/TestCase.php

use Laracasts\Integrated\Extensions\Laravel as IntegrationTest;

class IntegrationCase extends IntegrationTest {

	/**
	 * Creates the application.
	 *
	 * @return \Illuminate\Foundation\Application
	 */
	public function createApplication() {
		$app = require __DIR__ . '/../bootstrap/app.php';

		$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

		return $app;
	}

	public function setUpDb($seed = true) {
		Artisan::call('migrate');
		Mail::pretend(true);
		if($seed) {
			$this->seed();
			Artisan::call('db:seed', ['--class' => 'UsersTableSeeder']);
		}
	}

	public function setUp(){
		parent::setUp();
		$this->resetEvents();
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

}