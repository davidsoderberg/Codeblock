<?php namespace App\Repositories\Role;

use App\Repositories\IRepository;

/**
 * Interface RoleRepository
 * @package App\Repositories\Role
 */
interface RoleRepository extends IRepository
{

    /**
     * Fetch default role.
     *
     * @return mixed
     */
    public function getDefault();

    /**
     * Creates an array with id and role name.
     *
     * @return mixed
     */
    public function getSelectList();

    /**
     * Feth all roles and connect them with permissions.
     *
     * @param $permissions
     *
     * @return mixed
     */
    public function editRolePermission($permissions);

    /**
     * Updates connection beteewen role and permission.
     *
     * @param $input
     *
     * @return mixed
     */
    public function updateRolePermission($input);

    /**
     * Syncronize all permissions with a role.
     *
     * @param $role
     * @param $ids
     *
     * @return mixed
     */
    public function syncPermissions($role, $ids);
}
