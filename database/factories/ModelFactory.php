<?php


$factory->define(App\Post::class, function ($faker) {
	return [
		'name' => $faker->sentence(3),
		'cat_id' => $faker->numberBetween(1, 10),
		'description' => $faker->sentence(),
		'code' => $faker->sentence(),
		'user_id' => $faker->numberBetween(1, 10),
		'slug' => $faker->sentence(3),
	];
});

$factory->define(App\Notification::class, function ($faker) {
	return [
		'user_id' => $faker->numberBetween(1, 10),
		'type' => $faker->sentence(1),
		'subject' => $faker->sentence(),
		'body' => $faker->sentence(),
		'object_id' => $faker->numberBetween(1, 10),
		'object_type' => $faker->sentence(1),
		'from_id' => 1,
	];
});

$factory->define(App\Comment::class, function ($faker) {
	return [
		'comment' => $faker->sentence(),
		'post_id' => 1,
		'user_id' => 1,
	];
});

$factory->define(App\Forum::class, function ($faker) {
	return [
		'title' => $faker->sentence(3),
		'description' => $faker->sentence(),
	];
});

$factory->define(App\Topic::class, function ($faker) {
	return [
		'title' => $faker->sentence(3),
		'forum_id' => 1,
	];
});

$factory->define(App\Reply::class, function ($faker) {
	return [
		'reply' => $faker->sentence(),
		'topic_id' => 1,
		'user_id' => 1,
	];
});

$factory->define(\App\Team::class, function ($faker) {
	return [
		'name' => $faker->sentence(1),
		'owner_id' => 1,
	];
});