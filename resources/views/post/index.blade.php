@extends('master')

@section('css')

@stop

@section('content')
	<h2>Codeblocks</h2>
	{{ HTML::table(array('name', array('category' => 'categoryname'), 'description'), $posts, array('Edit' => 'PostController@edit', 'Delete' => 'PostController@delete', 'View' => 'PostController@show', 'Pagination' => 10), 'There are no code blocks right now.') }}
@stop

@section('script')

@stop