<?php

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Str;
use App\Services\PermissionAnnotation;

HTML::macro('avatar', function($value, $size = 48){
	$identicon = new \Identicon\Identicon();
	return $identicon->getImageDataUri($value, $size, '272822');
	//<img alt="Avatar for {{username}}" src="{{HTML::avatar(id)}}">
});

HTML::macro('markdown', function($text){
	$text = htmlentities($text);
	// Inspirerad och hämtat delar från: https://gist.github.com/jbroadway/2836900
	$rules = array (
		'/\[([^\[]+)\]\(([^\)]+)\)/' => '<a href=\'\2\'>\1</a>',
		'/(\*\*|__)(.*?)\1/' => '<strong>\2</strong>',
		'/(\*|_)(.*?)\1/' => '<i>\2</i>',
		'/\:\"(.*?)\"\:/' => '<q>\1</q>',
		//'/```(.*?)```/' => '<pre>\1</pre>',
		'/`(.*?)`/' => '<code>\1</code>'
	);
	foreach ($rules as $regex => $replacement) {
		$text = preg_replace($regex, $replacement, $text);
	}
	return $text;
});

HTML::macro('version', function($path){
	return asset($path).'?v='.filemtime(public_path().'/'.$path);
});

HTML::macro('mention', function($text){
	// Found at: http://granades.com/2009/04/06/using-regular-expressions-to-match-twitter-users-and-hashtags/
	return preg_replace('/(^|\s)@(\w+)/', ' <a class="mention" target="_blank" href="'.action('MenuController@index').'/user/\2">@\2</a>', $text);
});

/**
 * Visar alla meddelanderna i vyerna.
 */
HTML::macro('flash', function()
{
	$flash = array('success','error', 'warning', 'info');
	foreach ($flash as $value) {
		if(Session::has($value)) {
			return '<div class="text-center alert '.$value.'">' . Session::get($value) . ' <a href="#" class="close-alert">X</a></div>';
		}
	}
});

HTML::macro('adminmenu', function($content, $items){
	$list = '';
	foreach($items as $item){
		$list .= HTML::menulink($item[0], $item[1], false);
	}
	if($list == ''){
		return $list;
	}
	return '<li class="dropdown"><a class="hideUl" href="">'.$content.'</a><ul>'.$list.'</ul></li>';
});

HTML::macro('menulink', function($url = array('action' => '', 'params' => array()), $content, $attributes = array('target' =>'_self', 'class' => '', 'id' => ''), $optional = true){
	$link = HTML::actionlink($url, $content, $attributes, $optional);
	if($link !== ''){
		$link = '<li>'.$link.'</li>';
	}
	return $link;
});

HTML::macro('actionlink', function($url = array('action' => '', 'params' => array()), $content, $attributes = array('target' =>'_self', 'class' => '', 'id' => ''), $optional = true){
	$action = explode('@', $url['action']);
	$permissionAnnotation = New PermissionAnnotation('App\\Http\\Controllers\\'.$action[0], $action[1]);
	$permission = $permissionAnnotation->getPermission($optional);

	if (Auth::check() == false || Auth::check() && !Auth::user()->hasPermission($permission)){
		return '';
	}

	if(!isset($url['params'])){
		$url['params'] = array();
	}

	$link = '<a href="'.URL::action($url['action'], $url['params']).'"';
	if(isset($attributes['target'])) {
		$link .= ' target="'.$attributes['target'].'"';
	}
	if(isset($attributes['id'])){
		$link .= ' id="'.$attributes['id'].'"';
	}
	if(isset($attributes['class'])){
		$link .= ' class="'.$attributes['class'].'"';
	}
	$link .= '>';
	$link .= $content;
	$link .= '</a>';

	return $link;
});

// hämtat från laravels forum.
// Visar länka i vyerna
HTML::macro('nav_link', function($url, $text) {
	$class = ( Request::path() == $url ) ? ' class="active"' : '';
	return '<li'.$class.'><a href="'.URL::to($url).'">'.$text.'</a></li>';
});


// hämtat från ett kodsnuttsbibliotek för laravel.
// Visar tabeller i vyerna
HTML::macro('table', function($fields = array(), $data = array(), $show = array(), $info){

	if(count($data) > 0){
		$show = array_merge(array('Edit' => false, 'Delete' => false, 'View' => false, 'Pagination' => 0), $show);

		if(!is_array($data)){
			$data = $data->toArray();
		}

		if(!isset($_GET['page']) || !is_numeric($_GET['page'])){
			$_GET['page'] = 1;
		}

		$numberOfItems = count($data);

		if($show['Pagination'] > 0){
			if($_GET['page'] != 1) {
				$data = array_slice($data, ($_GET['page'] * $show['Pagination']) - $show['Pagination'], $show['Pagination']);
			}
			$paginator = new Paginator($data, $show['Pagination'], $_GET['page']);
			$paginator->setPath(Request::path());
		}

		$table = '<table>';
		$table .='<thead><tr>';
		foreach ($fields as $field)
		{
			$table .= '<th>' . str_replace('_',' ', Str::title($field)) . '</th>';
		}
		if ($show['Edit'] || $show['Delete'] || $show['View']){
			$table .= '<th>Actions</th>';
		}
		$table .= '</tr></thead>';

		foreach ( $data as $d )
		{
			$table .= '<tr>';
			foreach($fields as $key) {
				if($key != 'actions'){
					$value = null;
					if(is_object($d[$key])){
						$value = $d[$key]->name;
					}else{
						$value = $d[$key];
					}
					$table .= '<td data-title="'.str_replace('_',' ', Str::title($key)).'">' . $value . '</td>';
				}
			}
			if ($show['Edit'] || $show['Delete'] || $show['View'])
			{
				$table .= '<td data-title="Actions">';
				if ($show['Edit']){
					$table .= HTML::actionlink(array('action' => $show['Edit'] , 'params' => array($d['id'])), '<i class="fa fa-pencil"></i>Edit', array(), false).' ';
				}
				if ($show['View']){
					$table .= HTML::actionlink(array('action' => $show['View'], 'params' => array($d['id'])), '<i class="fa fa-eye"></i>View', array(), false).' ';
				}
				if ($show['Delete']){
					$table .= HTML::actionlink(array('action' => $show['Delete'], 'params' => array($d['id'])), '<i class="fa fa-trash-o"></i>Delete', array(), false).' ';
				}
				$table .= '</td>';
			}
			$table .= '</tr>';
		}
		$table .= '</table>';

		if($paginator->hasPages()){
			$table .= $paginator->render();
			/*
			$table .= '<ul>';
			for($i = 1; $i <= ceil($numberOfItems / $show['Pagination']); $i++){
				if($_GET['page'] == $i){
					$table .= '<li>'.$i.'</li>';
				}else{
					if($i == 1){
					$table .= '<li><a href="' . Request::path() . '">' . $i . '</a></li>';
					}else {
						$table .= '<li><a href="' . Request::path() . '?page=' . $i . '">' . $i . '</a></li>';
					}
				}
			}
			$table .= '</ul>';
			*/
		}
		return $table;
	}else{
		return '<div class="text-center alert info">'.$info.'</div>';
	}
});