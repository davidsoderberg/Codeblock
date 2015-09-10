@extends('master')

@section('css')

@stop

@section('content')
	@if(isset($category))
		<h2>All Codeblocks in category: {{ $category->name }}</h2>
	@elseif(isset($tag))
		<h2>All Codeblocks with tag: {{ $tag->name }}</h2>
	@else
		<h2>{{ $title }}</h2>
	@endif
	@if(isset($term))
		{{ Form::open(array('action' => 'PostController@search')) }}
			<div class="verticalRule noRule">
				<div class="float-left">
					{{ Form::select('category', $categories, $filter['category']) }}
				</div>
				<div class="float-right">
					{{ Form::select('tag', $tags, $filter['tag']) }}
				</div>
			</div>
			{{ Form::hidden('term', $term) }}
			<div class="text-center">
				{{ Form::button('Filter', array('type' => 'submit', 'class' => 'display-block width-100 float-none')) }}
			</div>
			<div class="horizontalRule margin-bottom-half margin-top-one"></div>
		{{ Form::close() }}
	@endif

	@if(count($posts) > 0)
		@if(!isset($term))
			<div class="margin-bottom-one">
				<label class="margin-bottom-half full-width-small">Sort by:</label>
				@if(isset($category))
					@include('partials.sortlinks', array('name' => $category->name, 'type' => 'category'))
				@else
					@if(isset($tag))
						@include('partials.sortlinks', array('name' => $tag->name, 'type' => 'tag'))
					@endif
				@endif
			</div>
		@endif
		@foreach ($posts as $post)
			@if($post->private != 1)
				<h3 class="text-left margin-top-half">{{HTML::actionlink($url = array('action' => 'PostController@show', 'params' => array($post->slug)), $post->name, array('class' => 'display-block decoration-none'))}}</h3>
				<div class="margin-bottom-half">
					<p>
						@if(!is_null($post->user))
							<i class="fa fa-user"></i> {{HTML::actionlink($url = array('action' => 'UserController@show', 'params' => array($post->user->username)), $post->user->username)}}
							<i class="fa fa-minus"></i>
						@endif
						<i class="fa fa-calendar"></i> {{ date('Y-m-d',strtotime($post['created_at'])) }}
					</p>
				</div>
				<p>{{ $post['description'] }}</p>
				<hr class="margin-bottom-half">
			@else
				@if(Auth::check())
					@if(Auth::user()->id == $post->user_id)
						<h3 class="text-left margin-top-half">{{HTML::actionlink($url = array('action' => 'PostController@show', 'params' => array($post->slug)), $post->name, array('class' => 'display-block decoration-none'))}}</h3>
						<div class="margin-bottom-half">
							<p>
								<i class="fa fa-user"></i> {{HTML::actionlink($url = array('action' => 'UserController@show', 'params' => array($post->user->username)), $post->user->username)}}
								<i class="fa fa-minus"></i>
								<i class="fa fa-calendar"></i> {{ date('Y-m-d',strtotime($post['created_at'])) }}
							</p>
						</div>
						<p>{{ $post['description'] }}</p>
						<hr class="margin-bottom-half">
					@endif
				@endif
			@endif
		@endforeach
	@else
		@if(isset($term))
			<div class="text-center alert info">There are no matches on: {{$term}}</div>
		@else
			<div class="text-center alert info">There are no blocks here yet.</div>
		@endif
	@endif
@stop

@section('script')

@stop