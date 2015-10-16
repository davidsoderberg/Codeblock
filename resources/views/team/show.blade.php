@extends('master')

@section('css')

@stop
@section('content')
	<h2>{{$team->name}}</h2>
	@if($team->owner_id == Auth::user()->id)
		<div class="margin-bottom-one margin-top-one inline-form">
			{{ Form::model($team, array('action' => array('TeamController@createOrUpdate', $team->id))) }}
			<div class="input-group">
				{{ Form::text('name', Input::old('name'), array('Placeholder' => 'Team name')) }}
				<span class="button-group">
						{{ Form::button('Update team', array('type' => 'submit')) }}
						</span>
			</div>
			{{ Form::close() }}
		</div>
	@endif
	<div class="verticalRule">
		<div class="float-left clearfix">
			<h3>Members</h3>
			@if($team->owner_id == Auth::user()->id)
				<div class="margin-bottom-one margin-top-one inline-form">
					{{ Form::model(null, array('action' => array('TeamController@invite'))) }}
					<div class="input-group">
						{{ Form::text('email', Input::old('email'), array('Placeholder' => 'Member email')) }}
						{{ Form::hidden('id', $team->id) }}
						<span class="button-group">
						{{ Form::button('Add member', array('type' => 'submit')) }}
						</span>
					</div>
					{{ Form::close() }}
				</div>
			@endif
			<div class="clearfix margin-bottom-half">
				{{HTML::actionlink($url = array('action' => 'UserController@show', 'params' => array($team->owner->username)), $team->owner->username)}}
			</div>
			@foreach($team->users as $user)
				<div class="clearfix margin-bottom-half">
					<span class="float-left">
						{{HTML::actionlink($url = array('action' => 'UserController@show', 'params' => array($user->username)), $user->username)}}
					</span>
					@if($team->owner_id == Auth::user()->id)
						<span class="float-right">
							{{HTML::actionlink($url = array('action' => 'TeamController@leave', 'params' => array($team->id)), '<i class="fa fa-sign-out"></i>', array('class' => 'confirm'))}}
						</span>
					@endif
				</div>
			@endforeach
		</div>
		<div class="float-right clearfix">
			<h3>Codeblocks</h3>
			@foreach($team->posts as $post)
				<div class="clearfix margin-bottom-half">
					{{HTML::actionlink($url = array('action' => 'PostController@show', 'params' => array($post->slug)), $post->name)}}
				</div>
			@endforeach
		</div>
	</div>
@stop

@section('script')

@stop