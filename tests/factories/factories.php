<?php

$factory('App\Post', [
	'name' => $faker->sentence(3),
	'category' => $faker->numberBetween(0,10),
	'description' => $faker->sentence(),
	'code' => $faker->sentence(),
	'user_id' => $faker->numberBetween(0,10),
	'slug' => $faker->sentence(3)
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