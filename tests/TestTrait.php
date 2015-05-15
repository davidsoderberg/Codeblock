<?php

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Artisan;

trait TestTrait{
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
			Artisan::call('db:seed', ['--class' => 'PermissionsTableSeeder']);
			Artisan::call('db:seed', ['--class' => 'RolesTableSeeder']);
			Artisan::call('db:seed', ['--class' => 'PermissionRoleTableSeeder']);
		}
	}
}