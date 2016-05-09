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
            <?php
            $input = Input::old('env['.$key.']');
            if (empty($input)) {
                $input = getenv($key);
            }
            ?>
            {{ Form::label($key, str_replace('_', ' ', $key).':') }}
            {{ Form::text('env['.$key.']', $input, array('id' => $key, 'placeholder' => $value, 'data-validator' => 'required')) }}
        @endif
    @endforeach
    <hr>
    {{ Form::button('Install', array('type' => 'submit')) }}
    {{ Form::close() }}
@stop

@section('script')
@stop