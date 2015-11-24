@extends('master')

@section('css')

@stop

@section('content')
	@if(is_null($id))
		<h2>Messages</h2>
		@if($threads->count() > 0)
			<div class="forum margin-bottom-one">
			@foreach($threads as $thread)
				<div class="item">
					{{HTML::actionlink($url = ['action' => 'UserController@show', 'params' => [$thread->creator()->username]], '<img alt="Avatar for '.$thread->creator()->username.'" src="'.HTML::avatar($thread->creator()->id).'">', ['class' => 'avatar'])}}
					<div class="reply">
						<h4>{{HTML::actionlink($url = ['action' => 'MessageController@index', 'params' => [$thread->id]], $thread->subject)}}</h4>
						<p class="font-bold">
							{{HTML::actionlink($url = ['action' => 'UserController@show', 'params' => [$thread->creator()->id]], $thread->creator()->username)}}
						</p>
						<p>{{ $thread->latestMessage->body }}</p>
					</div>
				</div>
			@endforeach
			</div>
		@else
			<p>Sorry, no threads.</p>
		@endif
		<h3>Create a new message</h3>
		{{ Form::model(null, ['action' => ['MessageController@createOrUpdate']]) }}

		{{ Form::label('subject', 'Subject') }}
		{{ Form::text('subject', Input::old('subject'), ['placeholder' => 'Subject of message', 'data-validator' => 'required|min:3']) }}
		{{ $errors->first('subject', '<div class="alert error">:message</div>') }}

		{{ Form::label('message', 'Message') }}
		{{ Form::textarea('message', Input::old('message'), ['placeholder' => 'Message', 'data-validator' => 'required|min:3']) }}
		{{ $errors->first('message', '<div class="alert error">:message</div>') }}

		@if($users->count() > 0)
			@foreach($users as $user)
				<label title="{{$user->username}}"><input type="checkbox" name="recipients[]" value="{{$user->id}}">{{$user->username}}</label>
			@endforeach
		@endif

		{{ Form::button('Create', ['type' => 'submit']) }}
		{{ Form::close() }}
	@else
		<h2>{{ $threads->subject }}</h2>
		<div class="forum margin-bottom-one">
			@foreach($threads->messages as $message)
				<div class="item">
					{{HTML::actionlink($url = ['action' => 'UserController@show', 'params' => [$message->user->username]], '<img alt="Avatar for '.$message->user->username.'" src="'.HTML::avatar($message->user->id).'">', ['class' => 'avatar'])}}
					<div class="reply">
						<p class="font-bold">
							{{HTML::actionlink($url = ['action' => 'UserController@show', 'params' => [$message->user->username]], $message->user->username)}},
							{{ $message->created_at->diffForHumans() }}
						</p>
						<p>{{ $message->body }}</p>
					</div>
				</div>
			@endforeach
		</div>

		<h3>Add a new message</h3>
		{{ Form::model($threads, ['action' => ['MessageController@createOrUpdate', $threads->id]]) }}

			{{ Form::label('message', 'Message') }}
			{{ Form::textarea('message', Input::old('message'), ['placeholder' => 'Message', 'data-validator' => 'required|min:3']) }}
			{{ $errors->first('message', '<div class="alert error">:message</div>') }}

			@if($users->count() > 0)
				@foreach($users as $user)
					<label title="{{ $user->username }}"><input type="checkbox" name="recipients[]" value="{{ $user->id }}">{{ $user->username }}</label>
				@endforeach
			@endif

			{{ Form::button('Create', ['type' => 'submit']) }}
		{{ Form::close() }}
	@endif
@stop

@section('script')

@stop