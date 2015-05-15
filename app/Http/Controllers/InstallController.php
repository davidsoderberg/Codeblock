<?php namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Redirect;
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
			if(Str::contains($key, 'REDIRECT') || Str::contains($key, 'APP') || Str::contains($key, 'DRIVER')){
				unset($envArray[$key]);
			}
		}
		return View::make('install')->with('title', 'Install')->with('options', $envArray);
	}

	/**
	 * @return mixed
	 */
	public function store(){

		$this->setEnv();

		$error = null;
		try {
			Artisan::call('install');
		} catch(\Exception $e){
			$error = $e->getMessage();
		}

		if(is_null($error)) {
			return Redirect::to('/')->with('success', 'You have now installed codeblock.');
		}else{
			if(is_null($error)){
				$error = 'We could not install codeblock for some reason, please try agian.';
			}
			return Redirect::back()->with('error', $error)->withInput($this->request->all());
		}
	}

	public function setEnv(){
		$input = $this->request->except('_token');
		$options = array_merge($this->getEnvArray(), $input);
		if(Str::contains(asset('/'), 'localhost')) {
			$options['APP_ENV'] = 'local';
		} else {
			$options['APP_ENV'] = 'production';
		}
		$options['APP_KEY'] = Str::random(12) . Str::random(12);
		foreach($options as $key => $value) {
			if($options[$key] == '') {
				$options[$key] = null;
			}
			if(Str::contains($key, 'REDIRECT')) {
				$options[$key] = asset('/') . $value;
			}
			putenv($key . '=' . $value);
			\Dotenv::setEnvironmentVariable($key, $value);
		}
		$options['APP_DEBUG'] = "false";

		$this->saveEnvArray($options);
		\Dotenv::load(base_path());
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
		return file_put_contents(base_path().'/.env', $file) > 0;
	}
}