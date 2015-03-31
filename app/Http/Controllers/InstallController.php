<?php namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

/**
 * Class InstallController
 * @package App\Http\Controllers
 */
class InstallController extends Controller {

	/**
	 * @return mixed
	 */
	public function install(){
		$envArray = $this->getEnvArray(true);
		foreach($envArray as $key => $value){
			if($value == 'null'){
				$envArray[$key] = '';
			}
			if(Str::contains($key, 'REDIRECT') || Str::contains($key, 'APP')){
				unset($envArray[$key]);
			}
		}
		return View::make('install')->with('title', 'Install')->with('options', $envArray);
	}

	/**
	 * @return mixed
	 */
	public function store(){
		$this->saveEnvArray(array_merge($this->getEnvArray(), Input::except('_token')));
		return Redirect::to('/')->with('success', 'You have now installed codeblock.');
	}

	/**
	 * @param bool $comment
	 * @return array
	 */
	private function getEnvArray($comment = false){
		$content = file_get_contents(base_path().'/.env.example');
		$envArray = explode('<br />', nl2br($content));
		$newenv = array();
		foreach($envArray as $env){
			$env = trim($env);
			if($env != ''){
				if(Str::contains($env, '#')){
					if($comment) {
						$newenv[] = str_replace('#', '', $env);
					}
				}else {
					$env = explode('=', $env);
					$newenv[$env[0]] = $env[1];
				}
			}
		}
		return $newenv;
	}

	/**
	 * @param $array
	 * @return int
	 */
	private function saveEnvArray($array){
		$file = '';
		foreach($array as $key => $value){
			$file .= $key.'='.$value.PHP_EOL;
		}
		return file_put_contents(base_path().'/.env.test', $file);
	}
}