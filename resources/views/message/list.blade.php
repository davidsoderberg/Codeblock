<h2>Messages</h2>
@if($threads->count() > 0)
	<div class="forum margin-bottom-one">
		@foreach($threads as $thread)
			<div class="item">
				{{HTML::actionlink($url = ['action' => 'UserController@show', 'params' => [$thread->creator()->username]], '<img alt="Avatar for '.$thread->latestMessage->user->username.'" src="'.HTML::avatar($thread->latestMessage->user->id).'">', ['class' => 'avatar'])}}
				<div class="reply">
					<h4>{{HTML::actionlink($url = ['action' => 'MessageController@index', 'params' => [$thread->id]], $thread->subject)}}</h4>
					<p class="font-bold">
						{{HTML::actionlink($url = ['action' => 'UserController@show', 'params' => [$thread->latestMessage->user->id]], $thread->latestMessage->user->username)}}
					</p>
					<p>{{ $thread->latestMessage->body }}</p>
				</div>
			</div>
		@endforeach
	</div>
@else
	<div class="text-center alert info">There are no threads here yet.</div>
@endif
<h3>Create a new message</h3>
{{ Form::model(null, ['action' => ['MessageController@createOrUpdate']]) }}

{{ Form::label('subject', 'Subject') }}
{{ Form::text('subject', Input::old('subject'), ['placeholder' => 'Subject of message', 'data-validator' => 'required|min:3']) }}
{{ $errors->first('subject', '<div class="alert error">:message</div>') }}

{{ Form::label('message', 'Message') }}
{{ Form::textarea('message', Input::old('message'), ['placeholder' => 'Message', 'data-validator' => 'required|min:3']) }}
{{ $errors->first('message', '<div class="alert error">:message</div>') }}

@if(!empty($users))
	{{ Form::label('', 'Recipients:') }}
	{{ Form::select('recipients[]', $users, '', array('multiple', 'class' => 'chosen-select', 'data-placeholder' => 'Choose some recipients')) }}
@endif

{{ Form::button('Create', ['type' => 'submit']) }}
{{ Form::close() }}