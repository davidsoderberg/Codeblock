@extends('master')

@section('css')

@stop

@section('content')
	<h2>Roles <a href="roles/create" class="button">Create</a></h2>
	{{ HTML::table(array('grade', 'name'), $roles, 'roles', array('Pagination' => 10, 'View' => false), 'There are no roles right now.') }}
@stop

@section('script')

@stop