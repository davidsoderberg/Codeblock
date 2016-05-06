<?php namespace App\Services;

use App\Repositories\User\EloquentUserRepository;
use App\Services\Annotation\Permission;
use Collective\Html\FormFacade;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Request;

/**
 * Class HtmlBuilder
 * @package App\Services
 */
class HtmlBuilder extends \Collective\Html\HtmlBuilder
{

	/**
	 * Creating user avatar for forum.
	 *
	 * @param $value
	 * @param int $size
	 *
	 * @return string
	 */
	public function avatar($value, $size = 48)
	{
		$identicon = new \Identicon\Identicon();

		return $identicon->getImageDataUri($value, $size, '272822');
		//<img alt="Avatar for {{username}}" src="{{HTML::avatar(id)}}">
	}

	/**
	 * Parse markdown string to html.
	 *
	 * @param $text
	 * @param bool|false $parseAll
	 *
	 * @return mixed|string
	 */
	public function markdown($text, $parseAll = false)
	{
		$parser = new Markdown($parseAll);

		return $parser->text(nl2br($text));
	}

	/**
	 * Adding version for assets files.
	 *
	 * @param $path
	 *
	 * @return string
	 */
	public function version($path)
	{
		return asset($path) . '?v=' . filemtime(public_path() . '/' . $path);
	}

	/**
	 * Adding link to mentioned user.
	 *
	 * @link http://granades.com/2009/04/06/using-regular-expressions-to-match-twitter-users-and-hashtags/
	 *
	 * @param $text
	 *
	 * @return mixed
	 */
	public function mention($text)
	{

		preg_match('/@(\w+)/', $text, $matches);
		if (count($matches) > 0) {
			$repo = new EloquentUserRepository();
			$prevMatch = '';
			foreach ($matches as $match) {
				$match = trim($match, '@');
				if ($match !== $prevMatch) {
					$user = $repo->get($match);
					if (!is_null($user)) {
						$text = preg_replace('/@(\w+)/',
							' <a class="mention" target="_blank" href="' . action('MenuController@index') . '/user/\1">@\1</a>',
							$text);
					}
				}
				$prevMatch = $match;
			}
		}
		return $text;
	}

	/**
	 * Creating flash message.
	 *
	 * @return string
	 */
	public function flash()
	{
		$flash = ['success', 'error', 'warning', 'info'];
		foreach ($flash as $value) {
			if (Session::has($value)) {
				return '<div class="text-center alert ' . $value . '">' . Session::get($value) . ' <a href="#" class="close-alert">X</a></div>';
			}
		}
	}

	/**
	 * Creating toast message.
	 * @return string
	 */
	public function toast()
	{
		$flash = ['success', 'error', 'warning', 'info'];
		foreach ($flash as $value) {
			if (Session::has($value)) {
				return '<div class="toast animated lightSpeedIn ' . $value . '"><a href="#" class="close-toast">X</a> ' . Session::get($value) . '</div>';
			}
		}
	}

	/**
	 * Creates an honeypot input field.
	 *
	 * @return string
	 */
	public function Honeypot()
	{
		return '<div class="display-none">' . FormFacade::input('text', 'honeyName') . '</div>';
	}

	/**
	 * Creating submenu.
	 *
	 * @param $content
	 * @param $items
	 *
	 * @return string
	 */
	public function submenu($content, $items)
	{
		$list = '';
		foreach ($items as $item) {
			$list .= $this->menulink($item[0], $item[1], [], false);
		}
		if ($list == '') {
			return $list;
		}

		return '<li class="dropdown"><a class="hideUl" href="">' . $content . '</a><ul>' . $list . '</ul></li>';
	}

	/**
	 * Checks if user has permission in view.
	 *
	 * @param $action
	 * @param bool $optional
	 *
	 * @return bool
	 */
	public function hasPermission($action, $optional = false)
	{
		$action = explode('@', $action);
		$permissionAnnotation = New Permission('App\\Http\\Controllers\\' . $action[0]);

		if (Auth::check() && !Auth::user()
				->hasPermission($permissionAnnotation->getPermission($action[1], $optional))
		) {
			return false;
		}

		return true;
	}

	/**
	 * Creating menulink.
	 *
	 * @param $url
	 * @param $content
	 * @param array $attributes
	 * @param bool $optional
	 *
	 * @return string
	 */
	public function menulink($url, $content, $attributes = [], $optional = true)
	{
		$link = $this->actionlink($url, $content, $attributes, $optional);
		if ($link !== '') {
			$link = '<li>' . $link . '</li>';
		}

		return $link;
	}

	/**
	 * Creating link.
	 *
	 * @param $url
	 * @param $content
	 * @param array $attributes
	 * @param bool $optional
	 *
	 * @return string
	 */
	public function actionlink($url, $content, $attributes = [], $optional = true)
	{
		if (!$this->hasPermission($url['action'], $optional)) {
			return '';
		}

		$url = array_merge(['action' => '', 'params' => []], $url);

		$attributes['href'] = URL::action($url['action'], $url['params']);
		if (Str::contains($attributes['href'], Request::path())) {
			if (isset($attributes['class'])) {
				$attributes['class'] .= ' active';
			} else {
				$attributes['class'] = 'active';
			}
		}

		return '<a' . $this->attributes($attributes) . '>' . $content . '</a>';
	}

	/**
	 * Creating table.
	 *
	 * @param array $fields
	 * @param array $data
	 * @param array $show
	 * @param $info
	 *
	 * @return string
	 */
	public function table($fields = [], $data = [], $show = [], $info)
	{

		if (count($data) > 0) {
			$show = array_merge(['Edit' => false, 'Delete' => false, 'View' => false, 'Pagination' => 0], $show);

			if (!is_array($data)) {
				$data = $data->toArray();
			}

			if (!isset($_GET['page']) || !is_numeric($_GET['page'])) {
				$_GET['page'] = 1;
			}

			$paginator = new LengthAwarePaginator($data, count($data), $show['Pagination'], $_GET['page'],
				['path' => Request::path()]);
			if ($show['Pagination'] > 0) {
				$data = array_slice($data, ($_GET['page'] * $show['Pagination']) - $show['Pagination'],
					$show['Pagination']);
			}

			$table = '<table>';
			$table .= '<thead><tr>';
			foreach ($fields as $key => $field) {
				if (is_array($field)) {
					$field = array_keys($field)[0];
				}
				$table .= '<th>' . str_replace('_', ' ', Str::title($field)) . '</th>';
			}
			if ($show['Edit'] || $show['Delete'] || $show['View']) {
				$table .= '<th>Actions</th>';
			}
			$table .= '</tr></thead>';

			foreach ($data as $d) {
				$table .= '<tr>';
				foreach ($fields as $key) {
					$array_key = null;
					if (is_array($key)) {
						$array_key = array_keys($key)[0];
						$key = $key[$array_key];
					}
					if ($key != 'actions') {
						$value = null;
						if (is_object($d[$key])) {
							$value = $d[$key]->name;
						} else {
							$value = $d[$key];
						}
						if (!is_null($array_key)) {
							$key = $array_key;
						}
						$table .= '<td data-title="' . str_replace('_', ' ',
								Str::title($key)) . '">' . $value . '</td>';
					}
				}
				if ($show['Edit'] || $show['Delete'] || $show['View']) {
					$table .= '<td data-title="Actions">';
					$showLink = false;
					if (isset($d['private'])) {
						if ($d['private'] == true) {
							$showLink = false;
						} else {
							$showLink = true;
						}
					}
					if (Auth::check() && isset($d['user_id'])) {
						$showLink = Auth::user()->id == $d['user_id'];
					}
					if ($show['Edit']) {
						$table .= $this->actionlink([
								'action' => $show['Edit'],
								'params' => [$d['id']],
							], '<i class="fa fa-pencil"></i>Edit', [], $showLink) . ' ';
					}
					if ($show['View']) {
						$table .= $this->actionlink([
								'action' => $show['View'],
								'params' => [$d['id']],
							], '<i class="fa fa-eye"></i>View', [], $showLink) . ' ';
					}
					if ($show['Delete']) {
						$table .= $this->actionlink([
								'action' => $show['Delete'],
								'params' => [$d['id']],
							], '<i class="fa fa-trash-o"></i>Delete', [], $showLink) . ' ';
					}
					$table .= '</td>';
				}
				$table .= '</tr>';
			}
			$table .= '</table>';

			$table .= $paginator->render();

			return $table;
		} else {
			return '<div class="text-center alert info">' . $info . '</div>';
		}
	}

	/**
	 * Creates script tags for codemirror javascripts files.
	 *
	 * @param $lang
	 */
	public function codemirror($lang)
	{
		$codemirror = new Codemirror();
		if (!is_array($lang)) {
			$lang = $codemirror->jsSwitch($lang);
		}
		foreach ($lang as $la) {
			if ($codemirror->modeExists($la)) {
				echo '<script src="' . asset('js/codemirror/mode/' . $la . '/' . $la . '.js') . '"></script>';
			}
		}
	}

	/**
	 * Creates excerpt.
	 *
	 * @param $text
	 * @param bool|false $parseAll
	 * @param int $words
	 *
	 * @return string
	 */
	public function excerpt($text, $parseAll = false, $words = 10)
	{
		return Str::words($this->markdown($text, $parseAll), $words);
	}

	/**
	 * Sorts links.
	 *
	 * @param $name
	 *
	 * @return string
	 */
	public function sortlink($name)
	{
		$parameters = Request::route()->parameters();

		if (isset($parameters['username'])) {
			$patterns = Route::getPatterns();
			if (preg_match('/' . $patterns['sort'] . '/', $parameters['username'])) {
				$parameters['sort'] = $parameters['username'];
			}
		}

		$url = Request::url();
		if (isset($parameters['sort'])) {
			$url = str_replace($parameters['sort'], strtolower($name), $url);
		} else {
			$url .= '/' . strtolower($name);
		}

		if (count(Request::all()) > 0) {
			$i = 0;
			foreach (Request::all() as $key => $value) {
				if ($i == 0) {
					$url .= '?';
				} else {
					$url .= '&';
				}
				$url .= $key . '=' . $value;
				$i++;
			}
		}

		$attributes = $this->attributes([
			'href' => $url,
			'class' => 'margin-bottom-half full-width-small float-none button',
		]);

		return '<a ' . $attributes . '>' . $name . '</a>';
	}
}