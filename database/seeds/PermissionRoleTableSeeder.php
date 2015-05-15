<?php

use Illuminate\Database\Seeder;
use \Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class PermissionRoleTableSeeder extends Seeder {

	/**
	 * Auto generated seed file
	 *
	 * @return void
	 */
	public function run()
	{
		Model::unguard();

		DB::table('permission_role')->truncate();
        
		DB::table('permission_role')->insert(array (

			array (
				'id' => 1,
				'permission_id' => 8,
				'role_id' => 2,
				'created_at' => '0000-00-00 00:00:00',
				'updated_at' => '0000-00-00 00:00:00',
			),

			array (
				'id' => 2,
				'permission_id' => 1,
				'role_id' => 2,
				'created_at' => '0000-00-00 00:00:00',
				'updated_at' => '0000-00-00 00:00:00',
			),

			array (
				'id' => 3,
				'permission_id' => 2,
				'role_id' => 2,
				'created_at' => '0000-00-00 00:00:00',
				'updated_at' => '0000-00-00 00:00:00',
			),

			array (
				'id' => 4,
				'permission_id' => 3,
				'role_id' => 2,
				'created_at' => '0000-00-00 00:00:00',
				'updated_at' => '0000-00-00 00:00:00',
			),

			array (
				'id' => 5,
				'permission_id' => 4,
				'role_id' => 2,
				'created_at' => '0000-00-00 00:00:00',
				'updated_at' => '0000-00-00 00:00:00',
			),

			array (
				'id' => 6,
				'permission_id' => 5,
				'role_id' => 2,
				'created_at' => '0000-00-00 00:00:00',
				'updated_at' => '0000-00-00 00:00:00',
			),

			array (
				'id' => 7,
				'permission_id' => 6,
				'role_id' => 2,
				'created_at' => '0000-00-00 00:00:00',
				'updated_at' => '0000-00-00 00:00:00',
			),

			array (
				'id' => 8,
				'permission_id' => 7,
				'role_id' => 2,
				'created_at' => '0000-00-00 00:00:00',
				'updated_at' => '0000-00-00 00:00:00',
			),

			array (
				'id' => 9,
				'permission_id' => 9,
				'role_id' => 2,
				'created_at' => '0000-00-00 00:00:00',
				'updated_at' => '0000-00-00 00:00:00',
			),

			array (
				'id' => 10,
				'permission_id' => 10,
				'role_id' => 2,
				'created_at' => '0000-00-00 00:00:00',
				'updated_at' => '0000-00-00 00:00:00',
			),

			array (
				'id' => 11,
				'permission_id' => 11,
				'role_id' => 2,
				'created_at' => '0000-00-00 00:00:00',
				'updated_at' => '0000-00-00 00:00:00',
			),

			array (
				'id' => 12,
				'permission_id' => 12,
				'role_id' => 2,
				'created_at' => '0000-00-00 00:00:00',
				'updated_at' => '0000-00-00 00:00:00',
			),

			array (
				'id' => 13,
				'permission_id' => 14,
				'role_id' => 2,
				'created_at' => '0000-00-00 00:00:00',
				'updated_at' => '0000-00-00 00:00:00',
			),

			array (
				'id' => 14,
				'permission_id' => 15,
				'role_id' => 2,
				'created_at' => '0000-00-00 00:00:00',
				'updated_at' => '0000-00-00 00:00:00',
			),

			array (
				'id' => 15,
				'permission_id' => 16,
				'role_id' => 2,
				'created_at' => '0000-00-00 00:00:00',
				'updated_at' => '0000-00-00 00:00:00',
			),

			array (
				'id' => 16,
				'permission_id' => 17,
				'role_id' => 2,
				'created_at' => '0000-00-00 00:00:00',
				'updated_at' => '0000-00-00 00:00:00',
			),

			array (
				'id' => 17,
				'permission_id' => 18,
				'role_id' => 2,
				'created_at' => '0000-00-00 00:00:00',
				'updated_at' => '0000-00-00 00:00:00',
			),

			array (
				'id' => 18,
				'permission_id' => 19,
				'role_id' => 2,
				'created_at' => '0000-00-00 00:00:00',
				'updated_at' => '0000-00-00 00:00:00',
			),

			array (
				'id' => 19,
				'permission_id' => 20,
				'role_id' => 2,
				'created_at' => '0000-00-00 00:00:00',
				'updated_at' => '0000-00-00 00:00:00',
			),

			array (
				'id' => 20,
				'permission_id' => 21,
				'role_id' => 2,
				'created_at' => '0000-00-00 00:00:00',
				'updated_at' => '0000-00-00 00:00:00',
			),

			array (
				'id' => 21,
				'permission_id' => 22,
				'role_id' => 2,
				'created_at' => '0000-00-00 00:00:00',
				'updated_at' => '0000-00-00 00:00:00',
			),

			array (
				'id' => 26,
				'permission_id' => 24,
				'role_id' => 2,
				'created_at' => '0000-00-00 00:00:00',
				'updated_at' => '0000-00-00 00:00:00',
			),

			array (
				'id' => 27,
				'permission_id' => 25,
				'role_id' => 2,
				'created_at' => '0000-00-00 00:00:00',
				'updated_at' => '0000-00-00 00:00:00',
			),

			array (
				'id' => 30,
				'permission_id' => 26,
				'role_id' => 2,
				'created_at' => '0000-00-00 00:00:00',
				'updated_at' => '0000-00-00 00:00:00',
			),

			array (
				'id' => 31,
				'permission_id' => 23,
				'role_id' => 2,
				'created_at' => '0000-00-00 00:00:00',
				'updated_at' => '0000-00-00 00:00:00',
			),

			array (
				'id' => 33,
				'permission_id' => 28,
				'role_id' => 2,
				'created_at' => '0000-00-00 00:00:00',
				'updated_at' => '0000-00-00 00:00:00',
			),

			array (
				'id' => 35,
				'permission_id' => 29,
				'role_id' => 2,
				'created_at' => '0000-00-00 00:00:00',
				'updated_at' => '0000-00-00 00:00:00',
			),

			array (
				'id' => 36,
				'permission_id' => 27,
				'role_id' => 2,
				'created_at' => '0000-00-00 00:00:00',
				'updated_at' => '0000-00-00 00:00:00',
			),
		));
	}

}
