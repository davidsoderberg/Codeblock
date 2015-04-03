<?php namespace App\Services;

class PermissionAnnotation{

	private $annotationService;
	private $permission;
	private $annotation = '@permission';

	public function __construct($class, $method){
		$this->annotationService = new AnnotationService($class, $this->annotation);
		$this->permission = $this->annotationService->getValues($method);
	}

	public function  getPermission($optional = false) {
		$permission = $this->permission;
		if($permission != '') {
			$permission = explode(':', $permission);
			$permission = $permission[0];
			if($optional) {
				if(isset($permission[1]) && strtolower($permission[1]) == 'optional') {
					$permission = '';
				}
			}
		}
		return $permission;
	}

}