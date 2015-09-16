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

	// Hittad på: https://github.com/laravel/framework/issues/1181
	protected function resetEvents()
	{
		$models = array(
			'App\Article',
			'App\Category',
			'App\Comment',
			'App\Forum',
			'App\Post',
			'App\Rate',
			'App\Reply',
			'App\Role',
			'App\Tag',
			'App\Topic',
			'App\User',
		);
		foreach ($models as $model) {
			call_user_func(array($model, 'flushEventListeners'));
			call_user_func(array($model, 'boot'));
		}
	}

}