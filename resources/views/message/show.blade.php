<h2>{{ $threads->subject }}</h2>
<div class="forum margin-bottom-one">
	@foreach($threads->messages as $message)
		<div class="item">
			{{HTML::actionlink($url = ['action' => 'UserController@show', 'params' => [$message->user->username]], '<img alt="Avatar for '.$message->user->username.'" src="'.HTML::avatar($message->user->id).'">', ['class' => 'avatar'])}}
			<div class="reply">
				<p class="font-bold">
					{{HTML::actionlink($url = ['action' => 'UserController@show', 'params' => [$message->user->username]], $message->user->username)}}, {{ $message->created_at->diffForHumans() }}
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

@if(!empty($users))
	{{ Form::label('', 'Recipients:') }}
	{{ Form::select('recipients[]', $users, '', ['multiple', 'class' => 'chosen-select', 'data-placeholder' => 'Choose some recipients']) }}
@endif

{{ Form::button('Create', ['type' => 'submit']) }}
{{ Form::close() }}