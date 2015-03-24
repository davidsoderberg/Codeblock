@extends('master')

@section('css')

@stop

@section('content')
	<h2>Forums</h2>
	{{ HTML::table(array('title', 'description'), $forums, 'forums', array('Pagination' => 10), 'There are no forums right now.') }}
@stop

@section('script')

@stop