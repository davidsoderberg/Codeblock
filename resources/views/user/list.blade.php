@extends('master')

@section('css')

@stop

@section('content')
	@if(isset($posts))
		<h2>{{ $user->username }}s Codeblocks</h2>
		@if(count($posts) > 0)
			<p>
				<b>Sort by:</b>
				@if(Auth::check() && Auth::user()->username === $user->username)
					{{HTML::actionlink($url = array('action' => 'UserController@listUserBlock'), 'date')}}
					<a href="{{URL::action('UserController@listUserBlock')}}/category">category</a>
				@else
					{{HTML::actionlink($url = array('action' => 'UserController@listUserBlock', 'params' => array($user->username)), 'date')}}
					<a href="{{URL::action('UserController@listUserBlock')}}/{{$user->username}}/category">category</a>
				@endif
			</p>
			@foreach ($posts as $post)
				@if($post->private != 1 || Auth::check() && Auth::user()->id == $post->user_id)
					<h3 class="text-left margin-top-half">{{HTML::actionlink($url = array('action' => 'PostController@show', 'params' => array($post->slug)),$post->name, array('class' => 'display-block decoration-none'))}}</h3>
					<div class="margin-bottom-half">
						<p>
							<i class="fa fa-user"></i> {{HTML::actionlink($url = array('action' => 'UserController@show', 'params' => array($post->user->username)), $post->user->username)}}
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
		{{ HTML::table(array('name', 'category', 'description'), $user->posts, array('Pagination' => 10, 'Edit' => 'PostController@edit', 'Delete' => 'PostController@delete', 'View' => 'PostController@show'), 'There are no codeblocks right now.') }}
	@endif
@stop

@section('script')

@stop