<?php

use Illuminate\Database\Seeder;
use \Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class RolesTableSeeder extends Seeder {

	/**
	 * Auto generated seed file
	 *
	 * @return void
	 */
	public function run()
	{
		Model::unguard();

		DB::table('roles')->truncate();
        
		DB::table('roles')->insert(array (

			array (
				'id' => 1,
				'name' => 'User',
				'grade' => 1,
				'created_at' => '2015-03-30 17:01:05',
				'updated_at' => '2015-05-11 06:44:00',
				'default' => 1,
			),

			array (
				'id' => 2,
				'name' => 'Admin',
				'grade' => 2,
				'created_at' => '2015-03-30 08:09:07',
				'updated_at' => '2015-05-11 06:44:00',
				'default' => 0,
			),
		));
	}

}
