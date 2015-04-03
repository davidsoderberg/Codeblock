@extends('master')

@section('css')

@stop

@section('content')
	@if(isset($posts))
		<h2>{{ $user->username }}s Codeblocks</h2>
		@if(count($posts) > 0)
			@foreach ($posts as $post)
				@if($post->private != 1 || Auth::check() && Auth::user()->id == $post->user_id)
					<h3>{{HTML::actionlink($url = array('action' => 'PostController@show', 'params' => array($post->id)), $post->name)}}</h3>
					<div class="margin-bottom-half">
						<p>
							<i class="fa fa-user"></i> {{HTML::actionlink($url = array('action' => 'UserController@show', 'params' => array($post->user->id)), $post->user->username)}}
							<i class="fa fa-minus"></i>
							<i class="fa fa-calendar"></i> {{ date('Y-m-d',strtotime($post->created_at)) }}
						</p>
					</div>
					<p>{{ $post->description }}</p>
					<hr class="margin-bottom-half">
				@endif
			@endforeach
		@else
			<div class="text-center alert info">{{ $user->username }} have no blocks yet.</div>
		@endif
	@else
		<h2>Your Codeblocks {{HTML::actionlink($url = array('action' => 'PostController@create'), 'Create')}}</h2>
		{{ HTML::table(array('name', 'category', 'description'), $user->posts, 'posts', array('Pagination' => 10), 'There are no code blocks right now.') }}
	@endif
@stop

@section('script')

@stop