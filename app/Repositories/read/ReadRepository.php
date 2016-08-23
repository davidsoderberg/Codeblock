<?php namespace App\Repositories\Read;

/**
 * Interface ReadRepository
 * @package App\Repositories\Read
 */
interface ReadRepository
{

    /**
     * Checks if user has read topic.
     *
     * @param $topic_id
     * @param $user_id
     *
     * @return mixed
     */
    public function hasRead($topic_id, $user_id = null);

    /**
     * Updates read model.
     *
     * @param $topic_id
     *
     * @return mixed
     */
    public function UpdatedRead($topic_id);
}
