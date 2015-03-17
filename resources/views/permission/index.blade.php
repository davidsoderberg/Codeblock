@extends('master')

@section('css')

@stop

@section('content')
	<h2>Permissions</h2>
	<div class="verticalRule">
		<div class="float-left">
			<h3>List of permissions</h3>
			{{ HTML::table(array('name', 'permission'), $permissions, 'permissions', array('Pagination' => 10, 'View' => false), 'There are no permissions right now.') }}
		</div>
		<div class="float-right">
			@if(isset($permission->id))
				{{ Form::model($permission, array('action' => array('PermissionController@createOrUpdate', $permission->id))) }}
			@else
				{{ Form::model($permission, array('action' => 'PermissionController@createOrUpdate')) }}
			@endif
				<h3>Create/update</h3>
				{{ Form::label('name','Name') }}
				{{ Form::text('name', Input::old('Name')) }}
				{{ $errors->first('name', '<div class="alert error">:message</div>') }}
				{{ Form::button('Create', array('type' => 'submit')) }}
			{{ Form::close() }}
		</div>
	</div>
@stop

@section('script')

@stop