<?php namespace App\Repositories\User;

use App\Repositories\IRepository;

/**
 * Interface UserRepository
 * @package App\Repositories\User
 */
interface UserRepository extends IRepository
{

	/**
	 * Fetch id by username.
	 *
	 * @param $username
	 *
	 * @return mixed
	 */
	public function getIdByUsername($username);

	/**
	 * Fetch id by email.
	 *
	 * @param $email
	 *
	 * @return mixed
	 */
	public function getIdByEmail($email);

	/**
	 * Updates user.
	 *
	 * @param $input
	 * @param $id
	 *
	 * @return mixed
	 */
	public function update($input, $id);

	/**
	 * Login user.
	 *
	 * @param $input
	 *
	 * @return mixed
	 */
	public function login($input);

	/**
	 * Send new password if user has forgot it.
	 *
	 * @param $input
	 *
	 * @return mixed
	 */
	public function forgotPassword($input);

	/**
	 * Activates user.
	 *
	 * @param $id
	 * @param $token
	 *
	 * @return mixed
	 */
	public function activateUser($id, $token);

}