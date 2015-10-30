@extends('master')

@section('css')

@stop

@section('content')
	<h2>Page not found</h2>
	<p class="text-center">
		@if(!isset($message))
			We can not found the page you are searching for, please try the search field.
		@else
			{{$message}}
		@endif
	</p>
@stop

@section('script')

@stop