<?php

use Illuminate\Database\Seeder;
use \Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class UsersTableSeeder extends Seeder {

	/**
	 * Auto generated seed file
	 *
	 * @return void
	 */
	public function run()
	{
		Model::unguard();

		DB::table('users')->truncate();

		DB::table('users')->insert(array (

			array (
				'id' => 1,
				'username' => 'david',
				'password' => \Illuminate\Support\Facades\Hash::make('test'),
				'email' => 'granskog_@hotmail.com',
				'active' => 1,
				'role' => 2,
				'remember_token' => 'GEo5aLIXvEEnDge6IaSWi3yZ6Q4GIrKKcSXmcfJrxX5HXxH7rJv12zZrGMHf',
				'created_at' => '2015-02-26 18:01:05',
				'updated_at' => '2015-03-02 05:47:23',
			),
		));
	}

}
