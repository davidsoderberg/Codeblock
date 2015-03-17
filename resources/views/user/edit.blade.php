@extends('master')

@section('css')
@stop

@section('content')
	<div class="verticalRule">
		<div class="float-left">
			{{ Form::model($user, array('action' => array('UserController@update', $user->id))) }}
				<h2>Edit {{ $user->username }}</h2>
				{{ Form::label('role', 'Role:') }}
				{{ Form::select('role', array(1 => 'User', 2 => 'Admin'), $user->role, array('data-validator' => 'required')) }}
				{{ $errors->first('role', '<div class="alert error">:message</div>') }}

				{{ Form::select('active', array(0 => 'Not active', 1 => 'Active'), $user->active, array('data-validator' => 'required')) }}
				{{ $errors->first('active', '<div class="alert error">:message</div>') }}

				{{ Form::button('Edit', array('type' => 'submit')) }}
			{{ Form::close() }}
		</div>
		<span class="text">OR</span>
		<div class="horizontalRule only-small"><span>OR</span></div>
		<div class="float-right">
			{{ Form::model($user, array('action' => array('UserController@delete', $user->id))) }}
				<h2>Delete {{ $user->username }}</h2>
				<div class="text-center">
					{{ Form::button('Delete', array('type' => 'submit', 'class' => 'float-none')) }}
				</div>
			{{ Form::close() }}
		</div>
	</div>
	<a href="/users"><i class="fa fa-arrow-left"></i></a>
@stop

@section('script')
@stop