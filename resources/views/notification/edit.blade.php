@extends('master')

@section('css')
@stop

@section('content')
	<h2>Send message</h2>
	{{ Form::model($notification, array('action' => 'NotificationController@createOrUpdate', $notification->id)) }}
	{{ Form::label('subject', 'Subject:') }}
	{{ Form::text('subject', $notification->subject, array('id' => 'subject', 'placeholder' => 'Subject of message', 'data-validator' => 'required|min:3')) }}
	{{ $errors->first('subject', '<div class="alert error">:message</div>') }}

	{{ Form::label('to_id', 'To:') }}<br />
	{{ Form::select('to_id', $teammates, $notification->user_id) }}

	{{ Form::label('body', 'Body:', array('class' =>'margin-top-one display-block')) }}
	{{ Form::textarea('body', $notification->body, array('id' => 'body', 'placeholder' => 'Body of message', 'data-validator' => 'required|min:3')) }}
	{{ $errors->first('body', '<div class="alert error">:message</div>') }}

	{{ Form::button('Send', array('type' => 'submit')) }}
	{{ Form::close() }}
@stop

@section('script')
@stop