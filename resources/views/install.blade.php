@extends('master')

@section('css')
@stop

@section('content')
	<h2>Install</h2>
	{{ Form::model(null, array('action' => 'InstallController@store')) }}
	@foreach($options as $key => $value)
		@if(is_numeric($key))
			<h3>{{$value}}</h3>
		@else
			{{ Form::label($key, str_replace('_', ' ', $key).':') }}
			@if(\Illuminate\Support\Str::contains($key, 'PRETEND'))
				{{Form::select($key, array('true' => 'yes', 'false' => 'no'), 'false')}}
			@else
				{{ Form::text($key, Input::old($key), array('id' => $key, 'placeholder' => $value, 'data-validator' => 'required')) }}
			@endif
		@endif
	@endforeach
	{{ Form::button('Install', array('type' => 'submit')) }}
	{{ Form::close() }}
@stop

@section('script')
@stop