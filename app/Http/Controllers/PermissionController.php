<?php namespace App\Http\Controllers;

use App\Repositories\Permission\PermissionRepository;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;

/**
 * Class PermissionController
 * @package App\Http\Controllers
 */
class PermissionController extends Controller
{

	/**
	 * Constructor for PermissionController
	 *
	 * @param PermissionRepository $permission
	 */
	public function __construct(PermissionRepository $permission)
	{
		parent::__construct();
		$this->permission = $permission;
	}

	/**
	 * Render index view for permissions.
	 * @param  int $id
	 * @return object
	 */
	public function index($id = null)
	{
		return View::make('permission.index')->with('title', 'Permissions')->with('permissions',
			$this->permission->get())->with('permission', $this->permission->get($id));
	}

	/**
	 * Creates or updates a permission.
	 * @param  int $id
	 * @return object
	 */
	public function createOrUpdate($id = null)
	{
		if ($this->permission->createOrUpdate($this->request->all(), $id)) {
			return Redirect::back()->with('success', 'The permission has been saved.');
		}

		return Redirect::back()->withErrors($this->permission->getErrors())->withInput($this->request->all());
	}

	/**
	 * Deletes a permission.
	 * @param  int $id
	 * @return object
	 */
	public function delete($id)
	{
		if ($this->permission->delete($id)) {
			return Redirect::back()->with('success', 'The permission has been deleted.');
		}

		return Redirect::back()->with('error', 'The permission could not be deleted.');
	}

}