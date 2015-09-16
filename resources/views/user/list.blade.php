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
					<a href="/user/list" class="margin-bottom-half full-width-small float-none button">Date</a>
					<a href="/user/list/name" class="margin-bottom-half full-width-small float-none button">Name</a>
					<a href="/user/list/category" class="margin-bottom-half full-width-small float-none button">Category</a>
					<a href="/user/list/stars" class="margin-bottom-half full-width-small float-none button">Stars</a>
					<a href="/user/list/comments" class="margin-bottom-half full-width-small float-none button">Comments</a>
				@else
					<a href="/user/list/{{$user->username}}" class="margin-bottom-half full-width-small float-none button">Date</a>
					<a href="/user/list/{{$user->username}}/name" class="margin-bottom-half full-width-small float-none button">Name</a>
					<a href="/user/list/{{$user->username}}/category" class="margin-bottom-half full-width-small float-none button">Category</a>
					<a href="/user/list/{{$user->username}}/stars" class="margin-bottom-half full-width-small float-none button">Stars</a>
					<a href="/user/list/{{$user->username}}/comments" class="margin-bottom-half full-width-small float-none button">Comments</a>
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
			@if(isset($paginator))
				{{$paginator->render()}}
			@endif
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