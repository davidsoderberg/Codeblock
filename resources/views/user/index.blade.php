@extends('master')

@section('css')

@stop

@section('content')
	<h2>Users</h2>
	{{ HTML::table(array('username', 'email', 'active', 'role', 'created_at'), $users, 'user', array('Pagination' => 10, 'Delete' => false), 'There are no users right now.') }}
@stop

@section('script')

@stop