<?php

use Illuminate\Database\Seeder;
use \Illuminate\Support\Facades\DB;

class TagsTableSeeder extends Seeder {

	/**
	 * Auto generated seed file
	 *
	 * @return void
	 */
	public function run()
	{
		DB::table('tags')->truncate();
        
		DB::table('tags')->insert(array (
			0 => 
			array (
				'id' => 1,
				'name' => 'Wordpress',
				'created_at' => '2014-04-08 05:14:14',
				'updated_at' => '2014-04-08 05:18:55',
			),
			1 => 
			array (
				'id' => 2,
				'name' => 'Drupal',
				'created_at' => '2014-04-08 05:15:22',
				'updated_at' => '2014-04-08 05:15:22',
			),
			2 => 
			array (
				'id' => 3,
				'name' => 'Joomla',
				'created_at' => '2014-04-08 05:15:27',
				'updated_at' => '2014-04-08 05:15:27',
			),
			3 => 
			array (
				'id' => 4,
				'name' => 'Laravel',
				'created_at' => '2014-04-08 05:15:33',
				'updated_at' => '2014-04-08 05:15:33',
			),
			4 => 
			array (
				'id' => 5,
				'name' => 'Symfony2',
				'created_at' => '2014-04-08 05:15:40',
				'updated_at' => '2014-04-08 05:15:40',
			),
			5 => 
			array (
				'id' => 6,
				'name' => 'Cakephp',
				'created_at' => '2014-04-08 05:15:48',
				'updated_at' => '2014-04-08 05:15:48',
			),
			6 => 
			array (
				'id' => 7,
				'name' => 'Codeigniter',
				'created_at' => '2014-04-08 05:15:55',
				'updated_at' => '2014-04-08 05:15:55',
			),
			7 => 
			array (
				'id' => 8,
				'name' => 'Slim',
				'created_at' => '2014-04-08 05:16:01',
				'updated_at' => '2014-04-08 05:16:01',
			),
			8 => 
			array (
				'id' => 9,
				'name' => 'Jquery',
				'created_at' => '2014-04-08 05:16:06',
				'updated_at' => '2014-04-08 05:16:06',
			),
			9 => 
			array (
				'id' => 10,
				'name' => 'Mysql',
				'created_at' => '2014-04-08 05:16:14',
				'updated_at' => '2014-04-08 05:16:14',
			),
			10 => 
			array (
				'id' => 11,
				'name' => 'Blade',
				'created_at' => '2014-04-08 05:16:20',
				'updated_at' => '2014-04-08 05:16:20',
			),
			11 => 
			array (
				'id' => 12,
				'name' => 'Twig',
				'created_at' => '2014-04-08 05:16:27',
				'updated_at' => '2014-04-08 05:16:27',
			),
			12 => 
			array (
				'id' => 13,
				'name' => 'Smarty',
				'created_at' => '2014-04-08 05:16:38',
				'updated_at' => '2014-04-08 05:16:38',
			),
			13 => 
			array (
				'id' => 14,
				'name' => 'Django',
				'created_at' => '2014-04-08 05:16:44',
				'updated_at' => '2014-04-08 05:16:44',
			),
			14 => 
			array (
				'id' => 15,
				'name' => 'Rails',
				'created_at' => '2014-04-08 05:16:49',
				'updated_at' => '2014-04-08 05:16:49',
			),
			15 => 
			array (
				'id' => 16,
				'name' => 'Yii',
				'created_at' => '2014-04-08 05:16:57',
				'updated_at' => '2014-04-08 05:16:57',
			),
			16 => 
			array (
				'id' => 17,
				'name' => 'Phalcon',
				'created_at' => '2014-04-08 05:17:03',
				'updated_at' => '2014-04-08 05:17:03',
			),
			17 => 
			array (
				'id' => 18,
				'name' => 'Kohana',
				'created_at' => '2014-04-08 05:17:10',
				'updated_at' => '2014-04-08 05:17:10',
			),
			18 => 
			array (
				'id' => 19,
				'name' => 'Less',
				'created_at' => '2014-04-08 05:17:16',
				'updated_at' => '2014-04-08 05:17:16',
			),
			19 => 
			array (
				'id' => 20,
				'name' => 'Sass',
				'created_at' => '2014-04-08 05:17:21',
				'updated_at' => '2014-04-08 05:17:21',
			),
		));
	}

}
