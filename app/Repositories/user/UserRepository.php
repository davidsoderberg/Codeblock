<?php namespace App\Repositories\User;

use App\Repositories\IRepository;

interface UserRepository extends IRepository {

	public function getIdByUsername($username);

	public function getIdByEmail($email);

	public function update($input, $id);

	public function login($input);

	public function forgotPassword($input);

	public function activateUser($id, $token);

}