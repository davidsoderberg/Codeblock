<?php

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Artisan;

trait TestTrait {

	protected $user;

	private $users = [
		['username' => 'david', 'password' => 'test'],
		['username' => 'codeblock', 'password' => 'test'],
	];

	protected function setUser( $position = 1 ) {
		if ( $position >= count( $this->users ) || $position < 1 ) {
			if ( $position < 1 ) {
				$position = 0;
			} else {
				$position = count( $this->users ) - 1;
			}
		} else {
			$position -= 1;
		}
		$this->user = $this->users[$position];
	}

	/**
	 * Creates the application.
	 *
	 * @return \Illuminate\Foundation\Application
	 */
	public function createApplication() {
		$app = require __DIR__ . '/../bootstrap/app.php';

		$app->make( 'Illuminate\Contracts\Console\Kernel' )->bootstrap();
		$this->setUser();

		return $app;
	}

	public function setUpDb( $seed = true ) {
		Artisan::call( 'migrate' );
		Mail::pretend( true );
		if ( $seed ) {
			$this->seed();
			Artisan::call( 'db:seed', ['--class' => 'UsersTableSeeder'] );
			Artisan::call( 'db:seed', ['--class' => 'PermissionsTableSeeder'] );
			Artisan::call( 'db:seed', ['--class' => 'RolesTableSeeder'] );
			Artisan::call( 'db:seed', ['--class' => 'PermissionRoleTableSeeder'] );
		}
	}

	// https://github.com/laravel/framework/issues/1181
	protected function resetEvents() {
		$files = \File::files( app_path() . '/Models' );

		foreach( $files as $i => $file ) {
			if ( !strpos( $file, '.php' ) ) {
				unset( $files[$i] );
			}
		}

		$files = str_replace( app_path() . '/Models/', '', $files );
		$models = str_replace( '.php', '', $files );

		$excludes = ['Model'];

		foreach( $excludes as $exclude ) {
			$key = array_search( $exclude, $models );
			if ( $key !== false ) {
				unset( $models[$key] );
			}
		}

		foreach( $models as $model ) {
			$model = 'App\\Models\\' . $model;

			if ( !method_exists( $model, 'flushEventListeners' ) ) {
				continue;
			}

			call_user_func( [$model, 'flushEventListeners'] );
			call_user_func( [$model, 'boot'] );
		}
	}

	public function removeField( array $data, $fields ) {
		if ( !is_array( $fields ) ) {
			$fields = [$fields];
		}

		foreach( $fields as $field ) {
			unset( $data[$field] );
		}

		return $data;
	}

	public function create( $model, array $overrides = [], $numbers = 1 ) {
		return factory( $model )->times( $numbers )->create( $overrides );
	}

}