<?php namespace App\Repositories\Role;

use App\Repositories\IRepository;

interface RoleRepository extends IRepository {

	public function getDefault();

	public function getSelectList();

	public function editRolePermission($permissions);

	public function updateRolePermission($input);

	public function syncPermissions($role, $ids);
}