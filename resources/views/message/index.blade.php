@extends('master')

@section('css')

@stop

@section('content')
	@if(is_null($id))
		@include('message.list')
	@else
		@include('message.show')
	@endif
@stop

@section('script')

@stop