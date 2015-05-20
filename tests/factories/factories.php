<?php

$factory('App\Post', [
	'name' => $faker->sentence(3),
	'cat_id' => $faker->numberBetween(1,10),
	'description' => $faker->sentence(),
	'code' => $faker->sentence(),
	'user_id' => $faker->numberBetween(1,10),
	'slug' => $faker->sentence(3)
]);

$factory('App\Notification', [
	'user_id' => $faker->numberBetween(1,10),
	'type' => $faker->sentence(1),
	'subject' => $faker->sentence(),
	'body' => $faker->sentence(),
	'object_id' => $faker->numberBetween(1,10),
	'object_type' => $faker->sentence(1),
	'sent_at' => $faker->date() ,
	'from_id' => 1
]);

$factory('App\Comment', [
	'comment' => $faker->sentence()
]);

$factory('App\Forum', [
	'title' => $faker->sentence(3),
	'description' => $faker->sentence()
]);

$factory('App\Topic', [
	'title' => $faker->sentence(3)
]);

$factory('App\Reply', [
	'reply' => $faker->sentence()
]);