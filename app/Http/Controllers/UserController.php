<?php namespace App\Http\Controllers;

use App\NotificationType;
use App\Repositories\Notification\NotificationRepository;
use App\Repositories\User\UserRepository;
use App\Social;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Laravel\Socialite\Contracts\Factory as Socialite;

class UserController extends Controller {

	public function __construct(UserRepository $user)
	{
		$this->user = $user;
	}

	/**
	 * Visar index vyn för användare
	 * @return objekt     objekt som innehåller allt som behövs i vyn
	 */
	public function index()
	{
		return View::make('user.index')->with('title', 'Users')->with('users', $this->user->get());
	}

	/**
	 * Visar vyn för att skapa en användare
	 * @return objekt     objekt som innehåller allt som behövs i vyn
	 */
	public function create()
	{
		return View::make('user.create')->with('title', Lang::get('app.userCreate'));
	}

	/**
	 * Skapa eller uppdaterar en användare.
	 * @param  int $id id på användaren som skall uppdateras.
	 * @return object     med värden dit användaren skall skickas.
	 */
	public function store($id = null)
	{
		if($this->user->createOrUpdate(Input::all(), $id)){
			if(is_null($id)){
				return Redirect::back()->with('success', 'Your user has been created, use the link in the mail to activate your user.');
			}else{
				return Redirect::back()->with('success', 'Your user has been saved.');
			}
		}
		return Redirect::back()->withInput(Input::except('password'))->withErrors($this->user->getErrors());
	}

	/**
	 * Visar vyn för en användare
	 * @param  int $id id för användaren som skall visas.
	 * @return objekt     objekt som innehåller allt som behövs i vyn
	 */
	public function show($id = 0)
	{
		if(Auth::check()) {
			if($id == 0){
				$id = Auth::user()->id;
			}
			$user = $this->user->get($id);
			return View::make('user.show')->with('title', $user->username)->with('user', $user);
		}
		return Redirect::action('UserController@listUserBlock', array($id));
	}

	public function showByUsername($username){
		return $this->show($this->user->getIdByUsername($username));
	}

	/**
	 * Visar en lista användaren block.
	 * @param  int $id id på anvädaren vars block skall listas.
	 * @return objekt     objekt som innehåller allt som behövs i vyn
	 */
	public function listUserBlock($id = 0){
		if($id == 0){
			if(Auth::check()) {
				$id = Auth::user()->id;
			}
		}

		$user = $this->user->get($id);
		if(is_null($user)){
			return Redirect::action('MenuController@browse');
		}
		return View::make('user.list')->with('title', $user->username)->with('user', $user)->with('posts', $user->posts);
	}

	/**
	 * Visar vyn för att redigera en användare.
	 * @param  int $id id för användaren som skall redigeras.
	 * @return objekt     objekt som innehåller allt som behövs i vyn
	 */
	public function edit($id)
	{
		$user = $this->user->get($id);
		return View::make('user.edit')->with('title', 'Edit User')->with('user', $user);
	}

	/**
	 * Uppdaterar användaren
	 * @param  int $id id på användaren som skall uppdateras.
	 * @return object     med värden dit användaren skall skickas.
	 */
	public function update(NotificationRepository $notification, $id)
	{
		if($this->user->update(Input::all(), $id)){
			$newUser = $this->user->get($id);
			if($newUser->active < 0){
				$notification->send($newUser->id, NotificationType::BANNED, $newUser);
			}
			if($newUser->role != Input::get('role') && Auth::user()->id != $newUser->id){
				$notification->send($newUser->id, NotificationType::ROLE, $newUser);
			}
			return Redirect::back()->with('success','You have change users rights.');
		}

		return Redirect::back()->withInput()->withErrors($this->user->getErrors());
	}

	/**
	 * ta bort användaren
	 * @param  int $id id på användaren som skall tas bort.
	 * @return object     med värden dit användaren skall skickas.
	 */
	public function destroy($id)
	{
		if($this->user->delete($id)){
			return Redirect::to('user/index')->with('success', 'The user has been deleted.');
		}

		return Redirect::back()->with('error', 'The user could not be deleted.');
	}

	/**
	 * Visar vyn för inloggning.
	 * @return objekt     objekt som innehåller allt som behövs i vyn
	 */
	public function login(){
		return View::make('login')->with('title', 'Login / Sign up');
	}

	/**
	 * Loggar in användaren
	 * @return object     med värden dit användaren skall skickas.
	 */
	public function Usersession()
	{
		if($this->user->login(Input::all())) {
			return Redirect::intended('/user')->with('success','You have logged in.');
		}
		return Redirect::to('/login')->with('error',"Your username or password is wrong, Don't forget to activate your user.")->withInput(array('loginUsername' => Input::get('loginUsername')));
	}

	/**
	 * Loggar ut användaren
	 * @return object     med värden dit användaren skall skickas.
	 */
	public function logout()
	{
		Auth::logout();
		return Redirect::to('/login')->with('success', 'You have logged out.');
	}

	/**
	 * Skickar ett nytt lösnord till användaren
	 * @return object     med värden dit användaren skall skickas.
	 */
	public function forgotPassword(){
		if($this->user->forgotPassword(Input::all())){
			return Redirect::back()->with('success', 'A new password have been sent to you.');
		}else{
			return Redirect::back()->with('error', "Your email don't exists in our database.");
		}
	}

	/**
	 * Aktiverar användaren
	 * @param  int $id id på användaren som skall aktiveras
	 * @param  string $token nycklen för att aktivera användaren.
	 * @return object     med värden dit användaren skall skickas.
	 */
	public function activate($id, $token){
		if($this->user->activateUser($id, $token)){
			Session::flash('success', 'Your user has been activated.');
			Auth::login($this->user->get($id));
			return Redirect::to('/user');
		}else{
			Session::flash('error', 'Something went wrong, please try again or contact admin.');
			return Redirect::to('/login');
		}
	}


	public function oauth($social, Socialite $socialite){
		if(Input::get('code') || Input::get('oauth_token') && Input::get('oauth_verifier')){
			$user = $socialite->driver($social)->user();
			if($social == 'github') {
				Session::put('github_access_token', $user->token);
			}
			if(Auth::check()) {
				$authedUser = Auth::user();
				$socials = $authedUser->socials;
				$create = true;
				if(count($socials) > 0) {
					foreach($socials as $soc) {
						if($social == $soc->social) {
							$create = false;
							break;
						}
					}
				}

				if($create) {
					$user = Social::create(array("social" => $social, "user_id" => $authedUser->id, "social_id" => $user->getId()));
					if($user) {
						return Redirect::to('/user')->with('success', 'You have connect ' . $social . ' to your account.');
					}
					return Redirect::to('/user')->with('error', 'We could not connect ' . $social . ' to your account.');
				} else {
					return Redirect::to('/user')->with('error', 'You have already connected ' . $social . ' to your account.');
				}
			}else{
				$socials = Social::all();
				if(count($socials) > 0) {
					foreach($socials as $soc) {
						if($social == $soc->social && $user->getId() == $soc->social_id) {
							Auth::loginUsingId($soc->user_id);
							return Redirect::to('/user')->with('success','You have logged in.');
						}
					}
				}
				return Redirect::to('/login')->with('error', 'We could not logg you in with your connected social media, please login with the login form and connect '.$social.' with your account.');
			}
		}
		return $socialite->driver($social)->redirect();
	}

}