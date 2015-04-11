@extends('master')

@section('css')

@stop

@section('content')
	<h2>Forums</h2>
	<div class="verticalRule">
		<div class="float-left">
			{{ HTML::table(array('title', 'description'), $forums, array('Edit' => 'ForumController@index', 'Delete' => 'ForumController@delete', 'View' => 'ForumController@show', 'Pagination' => 10), 'There are no forums right now.') }}
		</div>
		<div class="float-right">
			<h3>Create/edit forum</h3>
			@if(isset($forum->id))
				{{ Form::model($forum, array('action' => array('ForumController@createOrUpdate', $forum->id))) }}
			@else
				{{ Form::model($forum, array('action' => 'ForumController@createOrUpdate')) }}
			@endif
			{{ Form::label('Title', 'Title:') }}
			{{ Form::text('title', Input::old('title'), array('id' => 'title', 'placeholder' => 'Title of forum', 'data-validator' => 'required|min:3')) }}
			{{ $errors->first('title', '<div class="alert error">:message</div>') }}
			{{ Form::label('Description', 'Description:') }}
			{{ Form::textarea('description', Input::old('description'), array('id' => 'description', 'placeholder' => 'Description of forum', 'data-validator' => 'required|min:3')) }}
			{{ $errors->first('description', '<div class="alert error">:message</div>') }}
			{{ Form::button('Send', array('type' => 'submit')) }}
			{{ Form::close() }}
		</div>
	</div>
@stop

@section('script')

@stop