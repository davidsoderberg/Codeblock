@extends('master')

@section('css')

@stop

@section('content')
	<h2>Roles</h2>
	<div class="verticalRule">
		<div class="float-left">
			{{ HTML::table(array('grade', 'name'), $roles, array('Pagination' => 10, 'Edit' => 'RoleController@edit', 'Delete' => 'RoleController@delete'), 'There are no roles right now.') }}
		</div>
		<div class="float-right">
			<h3>Create role</h3>
			{{ Form::open(array('action' => array('RoleController@store'))) }}

			{{ Form::label('name','Name') }}
			{{ Form::text('name', Input::old('Name'), array('autofocus' => 'autofocus')) }}
			{{ $errors->first('name', '<div class="alert error">:message</div>') }}

			{{ Form::button('Create', array('type' => 'submit')) }}
			{{ Form::close() }}
			<div class="horizontalRule"><span>OR</span></div>
			<h3>Change default role</h3>
			{{ Form::open(array('action' => array('RoleController@setDefault'))) }}
			{{ Form::label('default', 'Default role') }}
			{{ Form::select('default', $selectList, $default); }}

			{{ Form::button('Set default', array('type' => 'submit')) }}
			{{ Form::close() }}
		</div>
	</div>
@stop

@section('script')

@stop