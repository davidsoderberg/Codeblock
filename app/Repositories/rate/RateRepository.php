<?php namespace App\Repositories\Rate;

/**
 * Interface RateRepository
 * @package App\Repositories\Rate
 */
interface RateRepository
{

    /**
     * Checks if user has give a comment a rate.
     *
     * @param $id
     *
     * @return mixed
     */
    public function check($id);

    /**
     * Calculates rate for comment.
     *
     * @param $id
     *
     * @return mixed
     */
    public function calc($id);

    /**
     * Creates a rate for a comment.
     *
     * @param $id
     * @param $type
     *
     * @return mixed
     */
    public function rate($id, $type);
}
