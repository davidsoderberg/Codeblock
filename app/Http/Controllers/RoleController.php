<?php namespace App\Http\Controllers;

use App\Repositories\Role\RoleRepository;
use App\Repositories\Permission\PermissionRepository;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Redirect;

/**
 * Class RoleController
 * @package App\Http\Controllers
 */
class RoleController extends Controller
{

    /**
     * Constructor for RoleController.
     *
     * @param RoleRepository $role
     * @param PermissionRepository $permission
     */
    public function __construct(RoleRepository $role, PermissionRepository $permission)
    {
        parent::__construct();
        $this->role = $role;
        $this->permission = $permission;
    }

    /**
     * Render index view for roles.
     * @permission view_role
     *
     * @param int $id
     *
     * @return object
     */
    public function index($id = null)
    {
        $defaultId = 0;
        $roles = $this->role->get();
        foreach ($roles as $role) {
            if ($role->default == 1) {
                $defaultId = $role->id;
                break;
            }
        }
        $role = null;
        if (!is_null($id)) {
            $role = $this->role->get($id);
        }

        return View::make('role.index')
            ->with('title', 'Roles')
            ->with('roles', $roles)
            ->with('selectList', $this->role->getSelectList())
            ->with('default', $defaultId)
            ->with('role', $role);
    }

    /**
     * Creates a role
     * @permission create_role
     *
     * @param int $id
     *
     * @return object
     */
    public function store($id = null)
    {
        if ($this->role->createOrUpdate($this->request->all(), $id)) {
            if (is_null($id)) {
                return Redirect::to('/roles')->with('success', 'The role has been created.');
            }

            return Redirect::to('/roles')->with('success', 'The role has been updated.');
        }

        return Redirect::back()->withErrors($this->role->getErrors())->withInput($this->request->all());
    }

    /**
     * Setter for default role.
     * @permission set_default_role
     *
     * @return mixed
     */
    public function setDefault()
    {
        if ($this->role->setDefault($this->request->get('default'))) {
            return Redirect::back()->with('success', 'The default role has been updated.');
        }

        return Redirect::back()->withErrors($this->role->getErrors())->withInput();
    }

    /**
     * Deletes a role.
     * @permission delete_role
     *
     * @param  int $id
     *
     * @return object
     */
    public function delete($id)
    {
        if (is_numeric($id) && $id != 1) {
            if ($this->role->delete($id)) {
                return Redirect::back()->with('success', 'The role has been deleted.');
            }
        }

        return Redirect::back()->with('error', 'The role could not be deleted.');
    }

    /**
     * Render view for combine roles with permissions.
     * @permission edit_permission
     * @return object
     */
    public function editRolePermission()
    {
        $roles = $this->role->editRolePermission($this->permission->get());

        return View::make('role.rolepermission')
            ->with('title', 'Add permission to Role')
            ->with('roles', $roles)
            ->with('permissions', $this->permission->get());
    }

    /**
     * Updates roles with permissions.
     * @permission edit_permission
     * @return object
     */
    public function updateRolePermission()
    {
        if ($this->role->updateRolePermission($this->request->all())) {
            return Redirect::back()->with('success', 'The role has now the permission you selected.');
        } else {
            return Redirect::back()->with('error', 'The role could not get the permissions you seleted.');
        }
    }
}
