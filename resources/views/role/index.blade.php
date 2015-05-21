@extends('master')

@section('css')

@stop

@section('content')
	<h2>Roles</h2>
	<div class="verticalRule">
		<div class="float-left">
			{{ HTML::table(array('name'), $roles, array('Pagination' => 10, 'Edit' => 'RoleController@index', 'Delete' => 'RoleController@delete'), 'There are no roles right now.') }}
		</div>
		<div class="float-right">
			@if(!is_null($role))
				<h3>Update role</h3>
				{{ Form::model($role, array('action' => array('RoleController@store', $role->id))) }}
			@else
				<h3>Create role</h3>
				{{ Form::open(array('action' => array('RoleController@store'))) }}
			@endif
			{{ Form::label('name','Name') }}
			{{ Form::text('name', Input::old('Name'), array('autofocus' => 'autofocus')) }}
			{{ $errors->first('name', '<div class="alert error">:message</div>') }}
				@if(!is_null($role))
	                {{ Form::button('Update', array('type' => 'submit')) }}
				@else
					{{ Form::button('Create', array('type' => 'submit')) }}
				@endif
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