<?php namespace App\Http\Controllers;

use App\Repositories\Permission\PermissionRepository;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;

class PermissionController extends Controller {

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

	public function __construct(PermissionRepository $permission)
	{
		parent::__construct();
		$this->permission = $permission;
	}

	/**
	 * Visar alla rättigheter och den rättigheten som skall redigeras
	 * @param  int $id id på den rättighet som skall redigeras
	 * @return object     med värden dit användaren skall skickas.
	 */
	public function index($id = null)
	{
		return View::make('permission.index')->with('title', 'Permissions')->with('permissions', $this->permission->get())->with('permission', $this->permission->get($id));
	}

	/**
	 * Skapar eller uppdaterar rättigheten.
	 * @param  int $id id på rättigheten som skall uppdateras.
	 * @return object     med värden dit användaren skall skickas.
	 */
	public function createOrUpdate($id = null){
		if($this->permission->createOrUpdate($this->request->all(), $id)){
			return Redirect::back()->with('success', 'The permission has been saved.');
		}

		return Redirect::back()->withErrors($this->permission->getErrors())->withInput($this->request->all());
	}

	/**
	 * Ta bort en rättighet
	 * @param  int $id id för rättigheten som skall tas bort.
	 * @return object     med värden dit användaren skall skickas.
	 */
	public function delete($id)
	{
		if($this->permission->delete($id)){
			return Redirect::back()->with('success', 'The permission has been deleted.');
		}

		return Redirect::back()->with('error', 'The permission could not be deleted.');
	}

}