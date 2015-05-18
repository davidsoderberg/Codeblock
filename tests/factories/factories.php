<?php

$factory('App\Post', [
	'name' => $faker->sentence(3),
	'category' => $faker->numberBetween(0,10),
	'description' => $faker->sentence(),
	'code' => $faker->sentence(),
	'user_id' => $faker->numberBetween(0,10),
	'slug' => $faker->sentence(3)
]);