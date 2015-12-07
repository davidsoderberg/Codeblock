<?php namespace App\Services;

/**
 * Class Codemirror
 * @package App\Services
 */
class Codemirror {

	/**
	 * Property to store codemirror dirs.
	 *
	 * @var array
	 */
	private $dirs;

	/**
	 * Constructor for Codemirror.
	 */
	public function __construct(){
		$this->dirs = scandir(public_path().'/js/codemirror/mode');
	}

	/**
	 * Checks if mode exists.
	 *
	 * @param $mode
	 *
	 * @return bool
	 */
	public function modeExists($mode){
		return in_array($mode, $this->dirs);
	}

	/**
	 * Switch categories that does not corresponding with javascripts categories for codemirror.
	 *
	 * @param $category
	 *
	 * @return array|string
	 */
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