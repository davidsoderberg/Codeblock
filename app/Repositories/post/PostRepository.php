<?php namespace App\Repositories\Post;

use App\Repositories\IRepository;

interface PostRepository extends IRepository {

	public function getId();

	public function getByCategory($id);

	public function getPopular($limit = 10, $min = 0);

	public function getNewest();

	public function getByTag($id);

	public function sort($posts, $sort = "date");

	public function duplicate($id);

	public function getForked($id);

	public function createOrDeleteStar($post_id);

	public function search($term, $filter = array('tag' => null, 'category' => null));
}