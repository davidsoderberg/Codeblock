<?php namespace App\Http\Controllers;

use App\Repositories\Category\CategoryRepository;
use App\Repositories\Tag\TagRepository;
use App\Repositories\CRepository;
use Illuminate\Support\Facades\Auth;
use Orangehill\Iseed\Facades\Iseed;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\View;

class MenuController extends Controller {

	/**
	 * Kör kommandon användaren anroppar via urlen.
	 * @param  string $command  Kommando användaren vill köra.
	 * @param  string $password Lösenord för att köra kommandona.
	 * @param  string $param    Paramter till kommandot.
	 * @return objekt     objekt som innehåller allt som behövs i vyn
	 */
	public function command($command, $password, $param = null){
		$content = '';
		$title = '';
		if($password == env('APP_KEY')){
			switch ($command) {
				case 'seed':
					Artisan::call('db:seed');
					$content = "<pre>".Lang::get('app.SeedingDone')."</pre>";
					$title = Lang::get('app.ArtisanSeed');
					break;
				case 'migrate':
					if($param != null){
						$call = 'migrate:'.$param;
					}else{
						$call = 'migrate';
					}
					Artisan::call($call);
					$content = "<pre>".Lang::get('app.MigrationDone')."</pre>";
					$title = Lang::get('app.ArtisanMigrate');
					break;
				case 'iseed':
					$title = Lang::get('app.ArtisanISeed');
					if($param == null){
						$param = 'users';
					}
					try {
						Iseed::generateSeed($param);
						$content = "<pre>".Lang::get('app.ISeedingDone', array('table' => $param))."</pre>";
						$content .= "<a href='/command/seed/".$password."'>".Lang::get('app.SeedNow')."<a>";
					} catch (Exception $e) {
						$content = "<pre>".Lang::get('app.ISeedingError', array('table' => $param))."</pre>";
					}
					break;
				case 'env':
					$title = Lang::get('app.CheckEnvironment');
					$content = app()->environment();
					break;
				default:
					$title = Lang::get('app.NoCommand');
					break;
			}
		}else{
			$title = Lang::get('app.WrongPassword');
		}
		return View::make('command')->with('title', $title)->with('content', $content);
	}

	/**
	 * Visar vyn för startsidan-
	 * @return objekt     objekt som innehåller allt som behövs i vyn
	 */
	public function index()
	{
		if(Auth::check()){
			return Redirect::action('MenuController@browse');
		}
		return View::make('index')->with('title', 'Home');
	}

	/**
	 * Visar vyn för att vissa alla kategorier och ettiketer.
	 * @return objekt     objekt som innehåller allt som behövs i vyn
	 */
	public function browse(CategoryRepository $category, TagRepository $tag)
	{
		return View::make('browse')->with('title', 'Browse')->with('tags', $tag->get())->with('categories', $category->get());
	}

	/**
	 * Visar vyn för licensen.
	 * @return objekt     objekt som innehåller allt som behövs i vyn
	 */
	public function license()
	{
		return View::make('license')->with('title', 'Codeblock License');
	}

	/**
	 * Visar vyn för kontaktformuläret.
	 * @return objekt     objekt som innehåller allt som behövs i vyn
	 */
	public function contact(){
		return View::make('contact')->with('title', 'Contact');
	}

	/**
	 * Vy för att skicka kontaktformuläret.
	 * @return object     med värden dit användaren skall skickas.
	 */
	public function sendContact(CRepository $ClassRepo){
		$rules = array('name' => 'required|min:3', 'email' => 'required|email', 'subject' => 'required|min:3', 'message' => 'required|min:3');
		$input = $this->request->all();

		foreach ($input as $key => $value) {
			$input[$key] = trim(strip_tags($value));
		}

		$v = Validator::make($input, $rules);

		if ($v->passes()) {
			$input['Cmessage'] = $input['message'];
			$emailInfo = array('toEmail' => env('FROM_ADRESS'), 'toName' => env('FROM_NAME'), 'subject' => 'Contact message');
			if($ClassRepo->sendEmail('emails.contact', $emailInfo, $input) == 1){
				return Redirect::back()->with('success', 'Your contact message have been send.');
			}
			return Redirect::back()->with('error', 'Sorry your contact message could not been send, please try again');
		}else{
			return Redirect::back()->withErrors($v->messages())->withInput();
		}
	}
}