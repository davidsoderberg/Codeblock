<?php namespace App\Services;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

/**
 * Class Github
 * @package App\Services
 */
class Github
{

	/**
	 * Checks if current user has requests to github left.
	 *
	 * @return bool
	 */
	public function hasRequestLeft()
	{
		$data = $this->githubCurl('rate_limit');
		if (is_array($data)) {
			return $data['rate']['remaining'] > 0;
		}
		return false;
	}

	/**
	 * Fetch gist.
	 *
	 * @param $id
	 *
	 * @return bool|mixed
	 */
	public function getGist($id)
	{
		$data = $this->githubCurl('gists/' . $id);
		if ($data) {
			return reset($data['files']);
		}
		return false;
	}

	/**
	 * Checks if token is a github token.
	 *
	 * @param $token
	 *
	 * @return bool
	 */
	public function isToken($token)
	{
		$data = $this->curl('https://api.github.com/?access_token=' . $token);
		if (isset($data['message'])) {
			return $data['message'] != "Bad credentials";
		}
		return true;
	}

	/**
	 * Method to curl to github.
	 *
	 * @param $url
	 *
	 * @return bool|mixed
	 */
	private function githubCurl($url)
	{
		$access_token = env('GITHUB_TOKEN', null);
		if (Session::has('github_access_token')) {
			$access_token = Session::get('github_access_token');
		}
		if ($this->isToken($access_token)) {
			return $this->curl('https://api.github.com/' . $url . '?access_token=' . $access_token);
		}
		return false;
	}

	/**
	 * Method to curl to an url.
	 *
	 * @param $url
	 *
	 * @return mixed
	 */
	private function curl($url)
	{
		$ch = curl_init();
		$username = 'Codeblock';
		if (Auth::check()) {
			$username = Auth::user()->username;
		}
		curl_setopt($ch, CURLOPT_USERAGENT, $username);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		$data = json_decode(curl_exec($ch), true);
		curl_close($ch);
		return $data;
	}
}