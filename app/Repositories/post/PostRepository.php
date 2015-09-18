<?php namespace App\Repositories\Post;

use App\Repositories\IRepository;
use App\Repositories\Star\StarRepository;

interface PostRepository extends IRepository {

	public function getId();

	public function getByCategory($id);

	public function getPopular($limit = 10, $min = 0);

	public function getNewest($limit = 10);

	public function getByTag($id);

	public function sort($posts, $sort = "date");

	public function duplicate($id);

	public function getForked($id);

	public function createOrDeleteStar(StarRepository $starRepository, $post_id);

	public function search($term, $filter = array('tag' => null, 'category' => null));
}