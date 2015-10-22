@extends('master')

@section('css')

@stop

@section('content')

	<h2>{{ $title }}</h2>

	@if(count(Auth::user()->inbox) > 0)
		@foreach (Auth::user()->inbox as $notification)
			<h3>{{$notification->subject}}</h3>
			<div class="clearfix margin-bottom-half">
				<p class="float-left">
					<i class="fa fa-user"></i> {{HTML::actionlink($url = array('action' => 'UserController@show', 'params' => array($notification->sender->username)), $notification->sender->username)}}
					<i class="fa fa-calendar"></i> {{$notification->created_at}}
				</p>
				<p class="float-right">
					<i class="fa fa-trash-o"></i> {{HTML::actionlink($url = array('action' => 'NotificationController@delete', 'params' => array($notification->id)), 'Delete')}}
				</p>
			</div>
			<p class="text-center">{{$notification->body}}</p>
			<div class="horizontalRule"></div>
		@endforeach
	@else
		<div class="text-center alert info">You have no notifications right now.</div>
	@endif
@stop

@section('script')

@stop