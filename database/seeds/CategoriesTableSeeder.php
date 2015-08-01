<?php

use Illuminate\Database\Seeder;
use \Illuminate\Support\Facades\DB;

class CategoriesTableSeeder extends Seeder {

	/**
	 * Auto generated seed file
	 *
	 * @return void
	 */
	public function run()
	{
		DB::table('categories')->truncate();
        
		DB::table('categories')->insert(array (
			0 => 
			array (
				'id' => 1,
				'name' => 'Html',
				'created_at' => '2014-04-08 05:12:35',
				'updated_at' => '2014-04-08 05:12:35',
			),
			1 => 
			array (
				'id' => 2,
				'name' => 'Css',
				'created_at' => '2014-04-08 05:12:42',
				'updated_at' => '2014-04-08 05:12:42',
			),
			2 => 
			array (
				'id' => 3,
				'name' => 'PHP',
				'created_at' => '2014-04-08 05:12:50',
				'updated_at' => '2014-04-08 05:12:50',
			),
			3 => 
			array (
				'id' => 4,
				'name' => 'Sql',
				'created_at' => '2014-04-08 05:12:55',
				'updated_at' => '2014-04-08 05:12:55',
			),
			4 => 
			array (
				'id' => 5,
				'name' => 'Javascript',
				'created_at' => '2014-04-08 05:13:05',
				'updated_at' => '2014-04-08 05:13:05',
			),
			5 => 
			array (
				'id' => 6,
				'name' => 'Ruby',
				'created_at' => '2014-04-08 05:13:16',
				'updated_at' => '2014-04-08 05:13:16',
			),
			6 => 
			array (
				'id' => 7,
				'name' => 'Python',
				'created_at' => '2014-04-08 05:13:22',
				'updated_at' => '2014-04-08 05:13:22',
			),
			7 =>
				array (
					'id' => 8,
					'name' => 'NoSql',
					'created_at' => '2014-04-08 05:13:22',
					'updated_at' => '2014-04-08 05:13:22',
			),
			8 =>
				array (
					'id' => 9,
					'name' => 'Dart',
					'created_at' => '2014-04-08 05:13:22',
					'updated_at' => '2014-04-08 05:13:22',
			),
			9 =>
				array (
					'id' => 10,
					'name' => 'Asp.net',
					'created_at' => '2014-04-08 05:13:22',
					'updated_at' => '2014-04-08 05:13:22',
			),
			10 =>
				array (
					'id' => 11,
					'name' => 'C#',
					'created_at' => '2014-04-08 05:13:22',
					'updated_at' => '2014-04-08 05:13:22',
			),
			11 =>
				array(
					'id' => 12,
					'name' => 'Other',
					'created_at' => '2014-04-08 05:13:22',
					'updated_at' => '2014-04-08 05:13:22',
				)
		));
	}

}
