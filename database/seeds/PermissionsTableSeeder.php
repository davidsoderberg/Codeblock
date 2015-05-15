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
				'name' => 'view private post',
				'permission' => 'view_private_post',
				'created_at' => '2015-04-09 02:55:25',
				'updated_at' => '2015-04-09 02:55:25',
			),

			array (
				'id' => 2,
				'name' => 'admin edit post',
				'permission' => 'admin_edit_post',
				'created_at' => '2015-04-09 02:55:25',
				'updated_at' => '2015-04-09 02:55:25',
			),

			array (
				'id' => 3,
				'name' => 'view role',
				'permission' => 'view_role',
				'created_at' => '2015-04-09 02:55:25',
				'updated_at' => '2015-04-09 02:55:25',
			),

			array (
				'id' => 4,
				'name' => 'create role',
				'permission' => 'create_role',
				'created_at' => '2015-04-09 02:55:25',
				'updated_at' => '2015-04-09 02:55:25',
			),

			array (
				'id' => 5,
				'name' => 'update role',
				'permission' => 'update_role',
				'created_at' => '2015-04-09 02:55:25',
				'updated_at' => '2015-04-09 02:55:25',
			),

			array (
				'id' => 6,
				'name' => 'set default role',
				'permission' => 'set_default_role',
				'created_at' => '2015-04-09 02:55:25',
				'updated_at' => '2015-04-09 02:55:25',
			),

			array (
				'id' => 7,
				'name' => 'delete role',
				'permission' => 'delete_role',
				'created_at' => '2015-04-09 02:55:25',
				'updated_at' => '2015-04-09 02:55:25',
			),

			array (
				'id' => 8,
				'name' => 'edit permission',
				'permission' => 'edit_permission',
				'created_at' => '2015-04-09 02:55:25',
				'updated_at' => '2015-04-09 02:55:25',
			),

			array (
				'id' => 9,
				'name' => 'view users',
				'permission' => 'view_users',
				'created_at' => '2015-04-09 15:23:10',
				'updated_at' => '2015-04-09 15:23:10',
			),

			array (
				'id' => 10,
				'name' => 'update users',
				'permission' => 'update_users',
				'created_at' => '2015-04-09 15:23:10',
				'updated_at' => '2015-04-09 15:23:10',
			),

			array (
				'id' => 11,
				'name' => 'delete users',
				'permission' => 'delete_users',
				'created_at' => '2015-04-09 15:23:10',
				'updated_at' => '2015-04-09 15:23:10',
			),

			array (
				'id' => 12,
				'name' => 'view tags',
				'permission' => 'view_tags',
				'created_at' => '2015-04-09 15:25:09',
				'updated_at' => '2015-04-09 15:25:09',
			),

			array (
				'id' => 14,
				'name' => 'delete tags',
				'permission' => 'delete_tags',
				'created_at' => '2015-04-09 15:25:09',
				'updated_at' => '2015-04-09 15:25:09',
			),

			array (
				'id' => 15,
				'name' => 'view forums',
				'permission' => 'view_forums',
				'created_at' => '2015-04-09 15:27:03',
				'updated_at' => '2015-04-09 15:27:03',
			),

			array (
				'id' => 16,
				'name' => 'create update forums',
				'permission' => 'create_update_forums',
				'created_at' => '2015-04-09 15:27:03',
				'updated_at' => '2015-04-09 15:27:03',
			),

			array (
				'id' => 17,
				'name' => 'delete forums',
				'permission' => 'delete_forums',
				'created_at' => '2015-04-09 15:27:03',
				'updated_at' => '2015-04-09 15:27:03',
			),

			array (
				'id' => 18,
				'name' => 'create update tags',
				'permission' => 'create_update_tags',
				'created_at' => '2015-04-09 15:27:04',
				'updated_at' => '2015-04-09 15:27:04',
			),

			array (
				'id' => 19,
				'name' => 'view categories',
				'permission' => 'view_categories',
				'created_at' => '2015-04-09 15:28:44',
				'updated_at' => '2015-04-09 15:28:44',
			),

			array (
				'id' => 20,
				'name' => 'create update categories',
				'permission' => 'create_update_categories',
				'created_at' => '2015-04-09 15:28:44',
				'updated_at' => '2015-04-09 15:28:44',
			),

			array (
				'id' => 21,
				'name' => 'delete categories',
				'permission' => 'delete_categories',
				'created_at' => '2015-04-09 15:28:44',
				'updated_at' => '2015-04-09 15:28:44',
			),

			array (
				'id' => 22,
				'name' => 'view comments',
				'permission' => 'view_comments',
				'created_at' => '2015-04-09 15:32:50',
				'updated_at' => '2015-04-09 15:32:50',
			),

			array (
				'id' => 23,
				'name' => 'view posts',
				'permission' => 'view_posts',
				'created_at' => '2015-04-09 15:32:50',
				'updated_at' => '2015-04-09 15:32:50',
			),

			array (
				'id' => 24,
				'name' => 'edit comments',
				'permission' => 'edit_comments',
				'created_at' => '2015-04-09 15:44:33',
				'updated_at' => '2015-04-09 15:44:33',
			),

			array (
				'id' => 25,
				'name' => 'delete comments',
				'permission' => 'delete_comments',
				'created_at' => '2015-04-09 15:44:33',
				'updated_at' => '2015-04-09 15:44:33',
			),

			array (
				'id' => 26,
				'name' => 'delete post',
				'permission' => 'delete_post',
				'created_at' => '2015-04-09 16:00:06',
				'updated_at' => '2015-04-09 16:00:06',
			),

			array (
				'id' => 27,
				'name' => 'create article',
				'permission' => 'create_article',
				'created_at' => '2015-04-14 02:54:54',
				'updated_at' => '2015-04-14 02:54:54',
			),

			array (
				'id' => 28,
				'name' => 'update article',
				'permission' => 'update_article',
				'created_at' => '2015-04-14 02:54:54',
				'updated_at' => '2015-04-14 02:54:54',
			),

			array (
				'id' => 29,
				'name' => 'delete article',
				'permission' => 'delete_article',
				'created_at' => '2015-04-14 02:54:54',
				'updated_at' => '2015-04-14 02:54:54',
			),
		));
	}

}
