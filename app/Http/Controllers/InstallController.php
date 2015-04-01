<?php namespace App\Http\Controllers;

use App\Repositories\CRepository;
use App\Repositories\Permission\PermissionRepository;
use App\Repositories\Role\RoleRepository;
use App\Services\Github;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
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
	public function store(RoleRepository $roleRepository, PermissionRepository $permissionRepository){
		$options = array_merge($this->getEnvArray(), Input::except('_token'));
		if(Str::contains(asset('/'), 'localhost')){
			$options['APP_ENV'] = 'local';
		}else{
			$options['APP_ENV'] = 'production';
		}
		$options['APP_KEY'] = Str::random(12).Str::random(12);
		foreach($options as $key => $value){
			if($options[$key] == ''){
				$options[$key] = null;
			}
			if(Str::contains($key, 'REDIRECT')){
				$options[$key] = asset('/').$value;
			}
			putenv($key.'='.$value);
			\Dotenv::setEnvironmentVariable($key, $value);
		}
		$options['APP_DEBUG'] = "false";

		$this->saveEnvArray($options);
		\Dotenv::load(base_path());

		Session::put('installationStep', '');
		try {
			if(Session::get('installationStep') == ''){
				$mail = New CRepository();
				$data = array('subject' => 'Test mail', 'body' => 'This is a mail to test mail configuration.');
				$emailInfo = array('toEmail' => env('FROM_ADRESS'), 'toName' => env('FROM_NAME'), 'subject' => 'Test mail');
				if(!$mail->sendEmail('emails.notification', $emailInfo, $data)) {
					throw new \Exception('The email is not configured correctly.');
				}
				Session::put('installationStep', 'Mail');
			}
			if(Session::get('installationStep') == 'Mail') {
				$github = new Github();
				if(!$github->isToken(env('GITHUB_TOKEN', null))) {
					throw new \Exception('The github token is not valid.');
				}
				Session::put('installationStep', 'Github');
			}
			if(Session::get('installationStep') == 'Github') {
				try {
					DB::connection()->getDatabaseName();
				} catch (\Exception $e){
					throw new \Exception('The database is not configured correctly.');
				}
				Session::put('installationStep', 'Database');
			}
			if(Session::get('installationStep') == 'Database') {
				try{
				Artisan::call('migrate');
				} catch (\Exception $e){
					throw new \Exception('The migration could not be done for some reason, please try agian.');
				}
				Session::put('installationStep', 'Migration');
			}
			if(Session::get('installationStep') == 'Migration') {
				try{
					Artisan::call('db:seed');
				} catch (\Exception $e){
					throw new \Exception('The seed of databe could not be done for some reason, please try agian.');
				}
				Session::put('installationStep', 'Seed');
			}
			if(Session::get('installationStep') == 'Seed') {
				try{
					Artisan::call('InsertPermissions');
				} catch (\Exception $e){
					throw new \Exception('The permissions colud not be created for some reason, please try agian.');
				}
				Session::put('installationStep', 'Permissions');
			}
			if(Session::get('installationStep') == 'Permissions') {
				$ids = array();
				foreach($permissionRepository->get() as $permission) {
					$ids[] = $permission->id;
				}
				if(!$roleRepository->createOrUpdate(array('name' => 'Super admin', 'default' => 1))) {
					throw new \Exception('The first role could not be created.');
				}
				if(!$roleRepository->syncPermissions($roleRepository->role, $ids)) {
					$roleRepository->delete($roleRepository->role->id);
					throw new \Exception('The first role could get any permissions please try agian.');
				}
				Session::put('installationStep', 'Role');
			}
		} catch(\Exception $e){
			return $this->installtionsError(array(Session::get('installationStep') => $e->getMessage()));
		}

		if($this->saveEnvArray($options) && Session::get('installationStep') == 'Role') {
			Session::put('installationStep', '');
			return Redirect::to('/')->with('success', 'You have now installed codeblock.');
		}else{
			return $this->installtionsError(array('Installation' => 'We could not install codeblock for some reason, please try agian.'));
		}
	}

	private function installtionsError($errors){
		return Redirect::back()->with('installtion_errors', $errors)->withInput(Input::all());
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