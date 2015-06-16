<?php namespace App\Repositories\Permission;

use App\Permission;
use App\Repositories\CRepository;

class EloquentPermissionRepository extends CRepository implements PermissionRepository {

	// hämtar en eller alla rättigheter.
	public function get($id = null)
	{
		if(is_null($id)){
			return Permission::all();
		}else{
			return Permission::find($id);
		}
	}

	// skapar eller uppdaterar en rättighet
	public function createOrUpdate($input, $id = null)
	{
		if(!is_numeric($id)) {
			$Permission = new Permission;
		} else {
			$Permission = Permission::find($id);
		}

		if(isset($input['permission'])){
			$input['permission'] = $this->stripTrim(str_replace('_', ' ', strtolower($input['permission'])));
			$Permission->permission = str_replace(' ', '_', $input['permission']);
		}

		if($Permission->save()){
			return true;
		}else{
			$this->errors = Permission::$errors;
			return false;
		}
	}

	// tar bort en rättighet
	public function delete($id){
		$Permission = Permission::find($id);
		if($Permission == null){
			return false;
		}
		$Permission->roles()->detach();
		return $Permission->delete();
	}

}