<?php namespace App\Http\Controllers;

use App\Repositories\Role\RoleRepository;
use App\Repositories\Permission\PermissionRepository;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;

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

	public function __construct(RoleRepository $role, PermissionRepository $permission)
	{
		$this->role = $role;
		$this->permission = $permission;
	}

	/**
	 * Visar index vyn för roller
	 * @return objekt     objekt som innehåller allt som behövs i vyn
	 */
	public function index()
	{
		$defaultId = 0;
		$roles = $this->role->get();
		foreach($roles as $role){
			if($role->default == 1){
				$defaultId = $role->id;
				break;
			}
		}
		return View::make('role.index')->with('title', 'Roles')->with('roles', $roles)->with('selectList', $this->role->getSelectList())->with('default', $defaultId);
	}

	/**
	 * Skapar en roll
	 * @return object     med värden dit användaren skall skickas.
	 */
	public function store(){
		if($this->role->createOrUpdate(Input::all())){
			return Redirect::back()->with('success', 'The role has been saved.');
		}
		return Redirect::back()->withErrors($this->role->getErrors())->withInput(Input::all());

	}

	/**
	 * Visar vyn för att redigera en roll
	 * @param  int $id id på rollen som skall redigeras
	 * @return objekt     objekt som innehåller allt som behövs i vyn
	 */
	public function edit($id)
	{
		return View::make('role.edit')->with('title', 'Edit role')->with('roles', $this->role->get());
	}

	/**
	 * Uppdatera en roll.
	 * @param  int $id id för rollen som skall uppdateras
	 * @return object     med värden dit användaren skall skickas.
	 */
	public function update(){
		if($this->role->update(Input::all())){
			return Redirect::back()->with('success', 'The role has been saved.');
		}else{
			return Redirect::back()->withErrors($this->role->getErrors())->withInput(Input::all());
		}
	}

	public function setDefault(){
		if($this->role->setDefault(Input::get('default'))){
			return Redirect::back()->with('success', 'The default role has been updated.');
		}
		return Redirect::back()->withErrors($this->role->getErrors())->withInput();
	}

	/**
	 * Ta bort en roll
	 * @param  int $id id för roll som skall tas bort.
	 * @return object     med värden dit användaren skall skickas.
	 */
	public function delete($id)
	{
		if($this->role->delete($id)){
			return Redirect::back()->with('success', 'The role has been deleted.');
		}

		return Redirect::back()->with('error', 'The role could not be deleted.');
	}

	/**
	 * Vissar vyn där användaren kombinerar roll med rättighet.
	 * @return objekt     objekt som innehåller allt som behövs i vyn
	 */
	public function editRolePermission() {
		$roles = $this->role->editRolePermission($this->permission->get());
		return View::make('role.rolepermission')->with('title', 'Add permission to Role')->with('roles', $roles)->with('permissions', $this->permission->get());
	}

	/**
	 * Kombinerar roll med rättighet.
	 * @return object     med värden dit användaren skall skickas.
	 */
	public function updateRolePermission() {
		if($this->role->updateRolePermission(Input::all())){
			return Redirect::back()->with('success', 'The role has now the permission you selected.');
		}else{
			return Redirect::back()->with('error', 'The role could not get the permissions you seleted.');
		}
	}

}