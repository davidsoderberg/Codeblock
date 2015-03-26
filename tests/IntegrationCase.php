<?php

use Laracasts\Integrated\Extensions\Laravel as IntegrationTest;
use Illuminate\Support\Facades\Artisan;

class IntegrationCase extends IntegrationTest {

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

	public function setUp(){
		parent::setUp();
		Artisan::call('migrate');
		Artisan::call('db:seed');
	}
}
