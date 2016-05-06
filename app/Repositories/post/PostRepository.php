<?php namespace App\Repositories\Post;

use App\Repositories\IRepository;
use App\Repositories\Star\StarRepository;

/**
 * Interface PostRepository
 * @package App\Repositories\Post
 */
interface PostRepository extends IRepository
{

    /**
     * Fetch id of current post.
     *
     * @return mixed
     */
    public function getId();

    /**
     * Fetch posts by category.
     *
     * @param $id
     *
     * @return mixed
     */
    public function getByCategory($id);

    /**
     * Fetch most popular posts.
     *
     * @param int $limit
     * @param int $min
     *
     * @return mixed
     */
    public function getPopular($limit = 10, $min = 0);

    /**
     * Fetch newest posts.
     *
     * @param int $limit
     *
     * @return mixed
     */
    public function getNewest($limit = 10);

    /**
     * Fetch posts by tag.
     *
     * @param $id
     *
     * @return mixed
     */
    public function getByTag($id);

    /**
     * Sort posts.
     *
     * @param $posts
     * @param string $sort
     *
     * @return mixed
     */
    public function sort($posts, $sort = "date");

    /**
     * Duplicates a post.
     *
     * @param $id
     *
     * @return mixed
     */
    public function duplicate($id);

    /**
     * Fetch all forked posts.
     *
     * @param $id
     *
     * @return mixed
     */
    public function getForked($id);

    /**
     * Add or removes a star for a post.
     *
     * @param StarRepository $starRepository
     * @param $post_id
     *
     * @return mixed
     */
    public function createOrDeleteStar(StarRepository $starRepository, $post_id);

    /**
     * Search on posts.
     *
     * @param $term
     * @param array $filter
     *
     * @return mixed
     */
    public function search($term, $filter = array('tag' => null, 'category' => null));
}
