<?php

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Artisan;

trait TestTrait {
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
	protected function resetEvents() {
		$files = \File::files(app_path());

		foreach($files as $i => $file) {
			if(!strpos($file, '.php')) {
				unset($files[$i]);
			}
		}

		$files = str_replace(app_path() . '/', '', $files);
		$models = str_replace('.php', '', $files);

		$excludes = ['Model'];

		foreach($excludes as $exclude) {
			$key = array_search($exclude, $models);
			if($key !== false) {
				unset($models[$key]);
			}
		}

		foreach($models as $model) {
			$model = 'App\\' . $model;

			if(!method_exists($model, 'flushEventListeners')) {
				continue;
			}

			call_user_func(array($model, 'flushEventListeners'));
			call_user_func(array($model, 'boot'));
		}
	}

}