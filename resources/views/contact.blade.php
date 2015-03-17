@extends('master')

@section('css')

@stop

@section('content')
    <h2>Contact</h2>

    <div class="text-center">
        You may contact me on <a href="mailto:info@codeblock.se">info@codeblock.se</a> or send me a contact message
        below.
    </div>
    {{ Form::open(array('action' => 'MenuController@sendContact')) }}
    {{ Form::label('contactName', 'Name:') }}
    {{ Form::text('name', Input::old('name'), array('id' => 'contactName', 'placeholder' => 'Name', 'data-validator' => 'required|min:3')) }}
    {{ $errors->first('name', '<div class="alert error">:message</div>') }}
    {{ Form::label('contactEmail', 'Email:') }}
    {{ Form::text('email', Input::old('email'), array('id' => 'contactEmail', 'placeholder' => 'Email', 'data-validator' => 'required|pattern:email')) }}
    {{ $errors->first('email', '<div class="alert error">:message</div>') }}
    {{ Form::label('contactSubject', 'Subject:') }}
    {{ Form::text('subject', Input::old('subject'), array('id' => 'contactSubject', 'placeholder' => 'Subject', 'data-validator' => 'required|min:3')) }}
    {{ $errors->first('subject', '<div class="alert error">:message</div>') }}
    {{ Form::label('contactMessage', 'Message:') }}
    {{ Form::textarea('message', Input::old('message'), array('id' => 'contactMessage', 'placeholder' => 'Message', 'data-validator' => 'required|min:3')) }}
    {{ $errors->first('message', '<div class="alert error">:message</div>') }}
    {{ Form::button('Send', array('type' => 'submit')) }}
    {{ Form::close() }}
@stop

@section('script')

@stop