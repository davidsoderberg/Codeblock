@extends('master')

@section('css')

@stop
@section('content')
	<h2>{{$title}}</h2>
	<div class="margin-bottom-one margin-top-one inline-form">
		{{ Form::model(null, array('action' => array('TeamController@createOrUpdate'))) }}
		<div class="input-group">
			{{ Form::text('name', Input::old('name'), array('Placeholder' => 'Team name')) }}
			<span class="button-group">
					{{ Form::button('Create team', array('type' => 'submit')) }}
					</span>
		</div>
		{{ $errors->first('name', '<div class="alert error">:message</div>') }}
		{{ Form::close() }}
	</div>
	<div class="verticalRule">
		<div class="float-left clearfix">
			<h3>Your teams</h3>
			@foreach(Auth::user()->ownedTeams as $team)
				<div class="clearfix margin-bottom-half">
					<span class="float-left">
						{{HTML::actionlink($url = array('action' => 'TeamController@listTeams', 'params' => array($team->id)), $team->name)}}
					</span>
					@if($team->owner_id == Auth::user()->id)
						<span class="float-right">
							{{HTML::actionlink($url = array('action' => 'TeamController@delete', 'params' => array($team->id)), '<i class="fa fa-trash-o"></i>', array('class' => 'confirm'))}}
						</span>
					@endif
				</div>
			@endforeach
		</div>
		<div class="float-right clearfix">
			<h3>Teams you are in</h3>
			@foreach(Auth::user()->teams as $team)
				<div class="clearfix margin-bottom-half">
					<span class="float-left">
						{{HTML::actionlink($url = array('action' => 'TeamController@listTeams', 'params' => array($team->id)), $team->name)}}
					</span>
					@if($team->owner_id != Auth::user()->id)
						<span class="float-right">
							{{HTML::actionlink($url = array('action' => 'TeamController@leave', 'params' => array($team->id)), '<i class="fa fa-sign-out"></i>', array('class' => 'confirm'))}}
						</span>
					@endif
				</div>
			@endforeach
		</div>
	</div>
@stop

@section('script')

@stop