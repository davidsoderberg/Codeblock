<?php namespace App\Repositories;

/**
 * Interface IRepository
 * @package App\Repositories
 */
interface IRepository
{

	/**
	 * Fetch one or all models.
	 *
	 * @param null $id
	 *
	 * @return mixed
	 */
	public function get($id = null);

	/**
	 * Creates or updates a model.
	 *
	 * @param $input
	 * @param null $id
	 *
	 * @return mixed
	 */
	public function createOrUpdate($input, $id = null);

	/**
	 * Delets a model.
	 *
	 * @param $id
	 *
	 * @return mixed
	 */
	public function delete($id);

}