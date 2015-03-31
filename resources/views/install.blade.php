@extends('master')

@section('css')
@stop

@section('content')
	<h2>Install</h2>
	{{ Form::model(null, array('action' => 'InstallController@store')) }}
	@if(isset($installtion_errors))
		{{$installtion_errors}}
	@endif
	@foreach($options as $key => $value)
		@if(is_numeric($key))
			<h3>{{$value}}</h3>
		@else
			{{ Form::label($key, str_replace('_', ' ', $key).':') }}
			{{ Form::text($key, Input::old($key), array('id' => $key, 'placeholder' => $value, 'data-validator' => 'required')) }}
		@endif
	@endforeach
	{{ Form::button('Install', array('type' => 'submit')) }}
	{{ Form::close() }}
@stop

@section('script')
@stop