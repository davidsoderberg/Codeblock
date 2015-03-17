@extends('master')

@section('css')
@stop

@section('content')
	{{ Form::model($comment, array('action' => array('CommentController@createOrUpdate', $comment->id))) }}
		<h2>Edit Comment</h2>
		<div class="verticalRule">
			<div class="float-left">
				{{ Form::label('commentUser', 'Made By:') }}
				{{ $comment->user->username }}
			</div>
			<div class="horizontalRule only-small"></div>
			<div class="float-right">
				{{ Form::label('commentPost', 'On:') }}
				{{ $comment->post->name }}
			</div>
		</div>
		<hr>
		<p class="margin-bottom-half margin-top-half">{{ Form::label('commentComment', 'Comment:') }} {{ $comment->comment }}</p>
		{{ Form::label('commentStatus', 'Status:') }}
		{{ Form::select('status', array(0 => 'Hidden', 1 => 'Shown'), $comment->status) }}
		{{ Form::button('Edit', array('type' => 'submit')) }}
	{{ Form::close() }}
@stop

@section('script')
@stop