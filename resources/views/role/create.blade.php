@extends('master')

@section('css')

@stop

@section('content')
	<h2>Create role</h2>
	{{ Form::open(array('action' => array('RoleController@store'))) }}

		{{ Form::label('name','Name') }}
		{{ Form::text('name', Input::old('Name'), array('autofocus' => 'autofocus')) }}
		{{ $errors->first('name', '<div class="alert error">:message</div>') }}

		{{ Form::button('Create', array('type' => 'submit')) }}
	{{ Form::close() }}
@stop

@section('script')

@stop