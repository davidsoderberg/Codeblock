<?php


$factory->define( \App\Models\Post::class, function ($faker) {
	return [
		'name' => $faker->sentence(3),
		'cat_id' => $faker->numberBetween(1, 10),
		'description' => $faker->sentence(),
		'code' => $faker->sentence(),
		'user_id' => $faker->numberBetween(1, 10),
		'slug' => $faker->sentence(3),
	];
});

$factory->define( \App\Models\Notification::class, function ($faker) {
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

$factory->define( \App\Models\Comment::class, function ($faker) {
	return [
		'comment' => $faker->sentence(),
		'post_id' => 1,
		'user_id' => 1,
	];
});

$factory->define( \App\Models\Forum::class, function ($faker) {
	return [
		'title' => $faker->sentence(3),
		'description' => $faker->sentence(),
	];
});

$factory->define( \App\Models\Topic::class, function ($faker) {
	return [
		'title' => $faker->sentence(3),
		'forum_id' => 1,
	];
});

$factory->define( \App\Models\Reply::class, function ($faker) {
	return [
		'reply' => $faker->sentence(),
		'topic_id' => 1,
		'user_id' => 1,
	];
});

$factory->define( \App\Models\Team::class, function ($faker) {
	return [
		'name' => $faker->sentence(1),
		'owner_id' => 1,
	];
});