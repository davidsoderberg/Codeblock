<?php

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Str;

HTML::macro('avatar', function($value, $size = 48){
	$identicon = new \Identicon\Identicon();
	return $identicon->getImageDataUri($value, $size, '272822');
	//<img alt="Avatar for {{username}}" src="{{HTML::avatar(id)}}">
});

HTML::macro('version', function($path){
	return asset($path).'?v='.filemtime(public_path().'/'.$path);
});

HTML::macro('mention', function($text){
	// Found at: http://granades.com/2009/04/06/using-regular-expressions-to-match-twitter-users-and-hashtags/
	return preg_replace('/(^|\s)@(\w+)/', '<a class="mention" target="_blank" href="'.action('MenuController@index').'/user/\2">@\2</a>', $text);
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

// hämtat från laravels forum.
// Visar länka i vyerna
HTML::macro('nav_link', function($url, $text) {
	$class = ( Request::path() == $url ) ? ' class="active"' : '';
	return '<li'.$class.'><a href="'.URL::to($url).'">'.$text.'</a></li>';
});


// hämtat från ett kodsnuttsbibliotek för laravel.
// Visar tabeller i vyerna
HTML::macro('table', function($fields = array(), $data = array(), $resource, $show = array(), $info){

	if(count($data) > 0){
		$show = array_merge(array('Edit' => true, 'Delete' => true, 'View' => true, 'Pagination' => 0), $show);

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
		if ($show['Edit'] || $show['Delete'] || $show['View'] || $d['actions'] ){
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
			if ($show['Edit'] || $show['Delete'] || $show['View'] || $d['actions'] )
			{
				$table .= '<td data-title="Actions">';
				if ($show['Edit']){
					$table .= '<a href="/' . $resource . '/edit'. '/' . $d['id'] . '"><i class="fa fa-pencil"></i>Edit</a> ';
				}
				if ($show['View']){
					$table .= '<a href="/' . $resource . '/' . $d['id'] . '"><i class="fa fa-eye"></i>View</a> ';
				}
				if ($show['Delete']){
					$table .= '<a href="/' . $resource . '/' . 'delete'. '/' . $d['id'] . '"><i class="fa fa-trash-o"></i>Delete</a> ';
				}
				if(isset($d['actions']) && $d['actions'] != ''){
					$table .= $d['actions'];
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