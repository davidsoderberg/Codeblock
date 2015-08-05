<?php namespace App\Repositories\Post;

use App\Repositories\IRepository;

interface PostRepository extends IRepository {

	public function getId();

	public function getByCategory($id);

	public function getByTag($id);

	public function duplicate($id);

	public function getForked($id);

	public function createOrDeleteStar($post_id);

	public function search($term);
}