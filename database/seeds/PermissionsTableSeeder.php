<?php

use Illuminate\Database\Seeder;
use \Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class PermissionsTableSeeder extends Seeder {

	/**
	 * Auto generated seed file
	 *
	 * @return void
	 */
	public function run()
	{
		Model::unguard();

		DB::table('permissions')->truncate();
        
		DB::table('permissions')->insert(array (

			array (
				'id' => 1,
				'permission' => 'view_private_post',
				'created_at' => '2015-04-09 02:55:25',
				'updated_at' => '2015-04-09 02:55:25',
			),

			array (
				'id' => 2,
				'permission' => 'admin_edit_post',
				'created_at' => '2015-04-09 02:55:25',
				'updated_at' => '2015-04-09 02:55:25',
			),

			array (
				'id' => 3,
				'permission' => 'view_role',
				'created_at' => '2015-04-09 02:55:25',
				'updated_at' => '2015-04-09 02:55:25',
			),

			array (
				'id' => 4,
				'permission' => 'create_role',
				'created_at' => '2015-04-09 02:55:25',
				'updated_at' => '2015-04-09 02:55:25',
			),

			array (
				'id' => 5,
				'permission' => 'update_role',
				'created_at' => '2015-04-09 02:55:25',
				'updated_at' => '2015-04-09 02:55:25',
			),

			array (
				'id' => 6,
				'permission' => 'set_default_role',
				'created_at' => '2015-04-09 02:55:25',
				'updated_at' => '2015-04-09 02:55:25',
			),

			array (
				'id' => 7,
				'permission' => 'delete_role',
				'created_at' => '2015-04-09 02:55:25',
				'updated_at' => '2015-04-09 02:55:25',
			),

			array (
				'id' => 8,
				'permission' => 'edit_permission',
				'created_at' => '2015-04-09 02:55:25',
				'updated_at' => '2015-04-09 02:55:25',
			),

			array (
				'id' => 9,
				'permission' => 'view_users',
				'created_at' => '2015-04-09 15:23:10',
				'updated_at' => '2015-04-09 15:23:10',
			),

			array (
				'id' => 10,
				'permission' => 'update_users',
				'created_at' => '2015-04-09 15:23:10',
				'updated_at' => '2015-04-09 15:23:10',
			),

			array (
				'id' => 11,
				'permission' => 'delete_users',
				'created_at' => '2015-04-09 15:23:10',
				'updated_at' => '2015-04-09 15:23:10',
			),

			array (
				'id' => 12,
				'permission' => 'view_tags',
				'created_at' => '2015-04-09 15:25:09',
				'updated_at' => '2015-04-09 15:25:09',
			),

			array (
				'id' => 14,
				'permission' => 'delete_tags',
				'created_at' => '2015-04-09 15:25:09',
				'updated_at' => '2015-04-09 15:25:09',
			),

			array (
				'id' => 15,
				'permission' => 'view_forums',
				'created_at' => '2015-04-09 15:27:03',
				'updated_at' => '2015-04-09 15:27:03',
			),

			array (
				'id' => 16,
				'permission' => 'create_update_forums',
				'created_at' => '2015-04-09 15:27:03',
				'updated_at' => '2015-04-09 15:27:03',
			),

			array (
				'id' => 17,
				'permission' => 'delete_forums',
				'created_at' => '2015-04-09 15:27:03',
				'updated_at' => '2015-04-09 15:27:03',
			),

			array (
				'id' => 18,
				'permission' => 'create_update_tags',
				'created_at' => '2015-04-09 15:27:04',
				'updated_at' => '2015-04-09 15:27:04',
			),

			array (
				'id' => 19,
				'permission' => 'view_categories',
				'created_at' => '2015-04-09 15:28:44',
				'updated_at' => '2015-04-09 15:28:44',
			),

			array (
				'id' => 20,
				'permission' => 'create_update_categories',
				'created_at' => '2015-04-09 15:28:44',
				'updated_at' => '2015-04-09 15:28:44',
			),

			array (
				'id' => 21,
				'permission' => 'delete_categories',
				'created_at' => '2015-04-09 15:28:44',
				'updated_at' => '2015-04-09 15:28:44',
			),

			array (
				'id' => 22,
				'permission' => 'view_comments',
				'created_at' => '2015-04-09 15:32:50',
				'updated_at' => '2015-04-09 15:32:50',
			),

			array (
				'id' => 23,
				'permission' => 'view_posts',
				'created_at' => '2015-04-09 15:32:50',
				'updated_at' => '2015-04-09 15:32:50',
			),

			array (
				'id' => 24,
				'permission' => 'edit_comments',
				'created_at' => '2015-04-09 15:44:33',
				'updated_at' => '2015-04-09 15:44:33',
			),

			array (
				'id' => 25,
				'permission' => 'delete_comments',
				'created_at' => '2015-04-09 15:44:33',
				'updated_at' => '2015-04-09 15:44:33',
			),

			array (
				'id' => 26,
				'permission' => 'delete_post',
				'created_at' => '2015-04-09 16:00:06',
				'updated_at' => '2015-04-09 16:00:06',
			),

			array (
				'id' => 27,
				'permission' => 'create_article',
				'created_at' => '2015-04-14 02:54:54',
				'updated_at' => '2015-04-14 02:54:54',
			),

			array (
				'id' => 28,
				'permission' => 'update_article',
				'created_at' => '2015-04-14 02:54:54',
				'updated_at' => '2015-04-14 02:54:54',
			),

			array (
				'id' => 29,
				'permission' => 'delete_article',
				'created_at' => '2015-04-14 02:54:54',
				'updated_at' => '2015-04-14 02:54:54',
			),
		));
	}

}
