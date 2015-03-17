@extends('master')

@section('css')

@stop

@section('content')
	<h2>Edit role</h2>
	{{ Form::open(array('action' => array('RoleController@update'))) }}

		@foreach ($roles as $role)
			{{ Form::label($role->inputname,$role->name) }}
			{{ Form::text('name[]', $role->name, array('autofocus' => 'autofocus')) }}
			{{ Form::selectRange('grade[]', 1, $count, $role->grade); }}
		@endforeach

		{{ Form::button('Update', array('type' => 'submit')) }}
	{{ Form::close() }}
@stop

@section('script')

@stop