<?php namespace App\Services;

class Codemirror {

	private $dirs;

	public function __construct(){
		$this->dirs = scandir(public_path().'/js/codemirror/mode');
	}

	public function modeExists($mode){
		return in_array($mode, $this->dirs);
	}

	// bytter ut de kategorier som inte stämmer överens med javascripts kategorierna hos codemirror.
	public function jsSwitch($category){
		$category = strtolower($category);
		$CodeMirrorcategories = array(
			'html' => 'xml',
			'c#' => 'clike',
			'asp.net' => 'clike',
			'php' => array('clike', 'xml', 'javascript', 'css', 'htmlmixed')
		);
		if(array_key_exists($category, $CodeMirrorcategories)){
			$current = $CodeMirrorcategories[$category];
			if(is_array($current)){
				$current = array_merge(array($category), $current);
			}
			$category = $current;
		}
		if(!is_array($category)){
			$category = array($category);
		}
		return $category;
	}
}