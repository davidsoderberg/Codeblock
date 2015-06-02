<?php namespace App\Http\Controllers;

use App\Repositories\Role\RoleRepository;
use App\Repositories\Permission\PermissionRepository;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Redirect;

/**
 * Class RoleController
 * @package App\Http\Controllers
 */
class RoleController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Default Home Controller
	|--------------------------------------------------------------------------
	|
	| You may wish to use controllers instead of, or in addition to, Closure
	| based routes. That's great! Here is an example controller method to
	| get you started. To route to this controller, just add the route:
	|
	|	Route::get('/', 'PostController@index');
	|
	*/

	/**
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
	 * Visar index vyn för roller
	 * @permission view_role
	 * @return objekt     objekt som innehåller allt som behövs i vyn
	 */
	public function index($id = null)
	{
		$defaultId = 0;
		$roles = $this->role->get();
		foreach($roles as $role){
			if($role->default == 1){
				$defaultId = $role->id;
				break;
			}
		}
		$role = null;
		if(!is_null($id)){
			$role = $this->role->get($id);
		}
		return View::make('role.index')->with('title', 'Roles')->with('roles', $roles)->with('selectList', $this->role->getSelectList())->with('default', $defaultId)->with('role', $role);
	}

	/**
	 * Skapar en roll
	 * @permission create_role
	 * @return object     med värden dit användaren skall skickas.
	 */
	public function store($id = null){
		if($this->role->createOrUpdate($this->request->all(), $id)){
			if(is_null($id)) {
				return Redirect::to('/roles')->with('success', 'The role has been created.');
			}
			return Redirect::to('/roles')->with('success', 'The role has been updated.');
 		}
 		return Redirect::back()->withErrors($this->role->getErrors())->withInput($this->request->all());

	}

	/**
	 * Sätter rollen som alla nya användare får.
	 * @permission set_default_role
	 * @return mixed
	 */
	public function setDefault(){
		if($this->role->setDefault($this->request->get('default'))){
			return Redirect::back()->with('success', 'The default role has been updated.');
		}
		return Redirect::back()->withErrors($this->role->getErrors())->withInput();
	}

	/**
	 * Ta bort en roll
	 * @permission delete_role
	 * @param  int $id id för roll som skall tas bort.
	 * @return object     med värden dit användaren skall skickas.
	 */
	public function delete($id)
	{
		if(is_numeric($id) && $id != 1) {
			if($this->role->delete($id)) {
				return Redirect::back()->with('success', 'The role has been deleted.');
			}
		}

		return Redirect::back()->with('error', 'The role could not be deleted.');
	}

	/**
	 * Vissar vyn där användaren kombinerar roll med rättighet.
	 * @permission edit_permission
	 * @return objekt     objekt som innehåller allt som behövs i vyn
	 */
	public function editRolePermission() {
		$roles = $this->role->editRolePermission($this->permission->get());
		return View::make('role.rolepermission')->with('title', 'Add permission to Role')->with('roles', $roles)->with('permissions', $this->permission->get());
	}

	/**
	 * Kombinerar roll med rättighet.
	 * @permission edit_permission
	 * @return object     med värden dit användaren skall skickas.
	 */
	public function updateRolePermission() {
		if($this->role->updateRolePermission($this->request->all())){
			return Redirect::back()->with('success', 'The role has now the permission you selected.');
		}else{
			return Redirect::back()->with('error', 'The role could not get the permissions you seleted.');
		}
	}

}