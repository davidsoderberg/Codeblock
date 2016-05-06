<?php namespace App\Repositories\Role;

use App\Models\Role;
use App\Repositories\CRepository;
use App\Services\CollectionService;

/**
 * Class EloquentRoleRepository
 * @package App\Repositories\Role
 */
class EloquentRoleRepository extends CRepository implements RoleRepository
{

	/**
	 * Property to store current role in.
	 *
	 * @var
	 */
	public $role;

	/**
	 * Fetch all roles.
	 *
	 * @param null $id
	 *
	 * @return \App\Services\Model|array|\Illuminate\Database\Eloquent\Collection|null|static
	 */
	public function get($id = null)
	{
		if (is_null($id)) {
			return $this->cache('all', Role::where('id', '!=', 0));
		}

		return CollectionService::filter($this->get(), 'id', $id, 'first');
	}

	/**
	 * Creates a role.
	 *
	 * @param $input
	 * @param null $id
	 *
	 * @return bool
	 */
	public function createOrUpdate($input, $id = null)
	{
		if (is_null($id)) {
			$Role = new Role;
		} else {
			$Role = $this->get($id);
		}
		if (isset($input['default']) && $input['default'] == 0 || isset($input['default']) && $input['default'] == 1) {
			$Role->default = $input['default'];
		}
		if (isset($input['name'])) {
			$Role->name = $this->stripTrim($input['name']);
		}
		if ($Role->save()) {
			$this->role = $Role;

			return true;
		} else {
			$this->errors = Role::$errors;

			return false;
		}
	}

	/**
	 * Fetch default role.
	 *
	 * @return \Illuminate\Database\Eloquent\Collection|static
	 */
	public function getDefault()
	{
		return CollectionService::filter($this->get(), 'default', 1, 'first');
	}

	/**
	 * Sets detfault role.
	 *
	 * @param $id
	 *
	 * @return bool
	 */
	public function setDefault($id)
	{
		if (is_numeric($id) && $id > 0) {
			$new = $this->get($id);
			if (!is_null($new)) {
				$current = $this->getDefault();
				if ($this->createOrUpdate(['default' => 0], $current->id)) {
					$new->default = 1;
					if ($new->save()) {
						return true;
					}
					$this->createOrUpdate(['default' => 1], $current->id);
				}
			}
		}

		return false;
	}

	/**
	 * Creates an array with id and role name.
	 *
	 * @return array
	 */
	public function getSelectList()
	{
		$roles = $this->get();
		$selectArray = [];
		foreach ($roles as $role) {
			$selectArray[$role->id] = $role->name;
		}

		return $selectArray;
	}

	/**
	 * Deletes a role.
	 *
	 * @param $id
	 *
	 * @return bool
	 */
	public function delete($id)
	{
		$Role = $this->get($id);
		if ($Role == null) {
			return false;
		}
		$Role->permissions()->detach();

		return $Role->delete();
	}

	/**
	 * Feth all roles and connect them with permissions.
	 *
	 * @param $permissions
	 *
	 * @return array
	 */
	public function editRolePermission($permissions)
	{

		$roles = $this->get();
		$permissions = $permissions->toArray();
		usort($permissions, function ($a, $b) {
			return strcmp($a['name'], $b['name']);
		});

		$roles_array = [];
		foreach ($roles as $role) {

			$role_permissions = [];
			foreach ($role->permissions as $permission) {
				$role_permissions[] = $permission->permission;
			}

			$roles_array[$role->name] = [];
			foreach ($permissions as $permission) {
				$roles_array[$role->name][$permission['permission']] = false;
				if (in_array($permission['permission'], $role_permissions)) {
					$roles_array[$role->name][$permission['permission']] = true;
				}
			}

		}

		return $roles_array;
	}

	/**
	 * Updates connection beteewen role and permission.
	 *
	 * @param $input
	 *
	 * @return bool
	 */
	public function updateRolePermission($input)
	{
		foreach ($this->get() as $role) {
			$roleName = str_replace(' ', '', $role->name);
			if (!array_key_exists($roleName, $input)) {
				$input[$roleName] = [];
			}
			if (!$this->syncPermissions($role, $input[$roleName])) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Syncronize all permissions with a role.
	 *
	 * @param $role
	 * @param $ids
	 *
	 * @return mixed
	 */
	public function syncPermissions($role, $ids)
	{
		if (!is_array($ids)) {
			$ids = [];
		}

		return $role->permissions()->sync($ids);
	}

}