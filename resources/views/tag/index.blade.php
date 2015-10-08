@extends('master')

@section('css')

@stop

@section('content')
	<h2>Tags</h2>
	<div class="verticalRule">
		<div class="float-left">
			<h3>List of tags</h3>
			{{ HTML::table(array('name'), $tags, array('Edit' => 'TagController@index', 'Delete' => 'TagController@delete', 'Pagination' => 10), 'There are no tags right now.') }}
		</div>
		<div class="float-right">
			@if(isset($tag->id))
				{{ Form::model($tag, array('action' => array('TagController@createOrUpdate', $tag->id))) }}
			@else
			{{ Form::model($tag, array('action' => 'TagController@createOrUpdate')) }}
			@endif
				<h3>Make/update</h3>
				{{ Form::label('Name', 'Name:') }}
				{{ Form::text('name', Input::old('name'), array('id' => 'Name', 'placeholder' => 'Name of tag', 'data-validator' => 'required|min:3')) }}
				{{ $errors->first('name', '<div class="alert error">:message</div>') }}
				{{ Form::button('Send', array('type' => 'submit')) }}
			{{ Form::close() }}
		</div>
	</div>
@stop

@section('script')

@stop