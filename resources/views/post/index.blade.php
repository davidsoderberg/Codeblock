@extends('master')

@section('css')

@stop

@section('content')
	<h2>Codeblocks</h2>
	{{ HTML::table(array('name', 'category', 'description'), $posts, 'posts', array('Pagination' => 10), 'There are no code blocks right now.') }}
@stop

@section('script')

@stop