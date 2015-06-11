<?php


$factory->define('App\Post', function ($faker) {
	return [
		'name' => $faker->sentence(3),
		'cat_id' => $faker->numberBetween(1,10),
		'description' => $faker->sentence(),
		'code' => $faker->sentence(),
		'user_id' => $faker->numberBetween(1,10),
		'slug' => $faker->sentence(3)
	];
});

$factory->define('App\Notification', function ($faker) {
	return [
		'user_id' => $faker->numberBetween(1,10),
		'type' => $faker->sentence(1),
		'subject' => $faker->sentence(),
		'body' => $faker->sentence(),
		'object_id' => $faker->numberBetween(1,10),
		'object_type' => $faker->sentence(1),
		'sent_at' => $faker->date(),
		'from_id' => 1
	];
});

$factory->define('App\Comment', function ($faker) {
	return [
		'comment' => $faker->sentence()
	];
});

$factory->define('App\Forum', function ($faker) {
	return [
		'title' => $faker->sentence(3),
		'description' => $faker->sentence()
	];
});

$factory->define('App\Topic', function ($faker) {
	return [
		'title' => $faker->sentence(3)
	];
});

$factory->define('App\Reply', function ($faker) {
	return [
		'reply' => $faker->sentence()
	];
});