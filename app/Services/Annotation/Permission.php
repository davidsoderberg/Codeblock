<?php namespace App\Services\Annotation;

class Permission extends Annotation{

	protected $annotation = '@permission';

	public function getPermission($method, $optional = false) {
		$permission = $this->getValues($method);
		if($permission != '') {
			$permission = explode(':', $permission);
			if($optional) {
				if(isset($permission[1]) && strtolower($permission[1]) == 'optional') {
					$permission = '';
				}
			}else{
				$permission = $permission[0];
			}
		}
		return $permission;
	}

	public function getPermissions(){
		return $this->getValues();
	}

	public function getMethods(){
		return array_keys($this->getValues());
	}
}