@extends('master')

@section('css')

@stop

@section('content')
	@if($article == null)
		<h2>Blog</h2>
	@endif
	@if(count($articles) > 0)
	@foreach($articles as $art)
		@if($article == null)
			<h3 class="margin-top-one">{{HTML::actionlink($url = array('action' => 'ArticleController@index', 'params' => array($art->slug)), $art->title)}}</h3>
		@else
			<h2>{{$art->title}}</h2>
		@endif
		<p class="no-margin font-bold">
			{{$art->created_at->diffForHumans()}}
			@if(Auth::check())
			<span class="float-right">
				@if(HTML::hasPermission('ArticleController@delete'))
					{{HTML::actionlink($url = array('action' => 'ArticleController@delete', 'params' => array($art->id)), '<i class="fa fa-trash-o"></i>')}}
				@endif
				@if(HTML::hasPermission('ArticleController@create') && $article == null)
					{{HTML::actionlink($url = array('action' => 'ArticleController@index', 'params' => array($art->id)), '<i class="fa fa-pencil"></i>')}}
				@endif
			</span>
			@endif
		</p>
		@if($article == null)
			<p>{{HTML::excerpt($art->body, true)}}</p>
		@else
			<p>{{HTML::markdown($art->body, true)}}</p>
		@endif
	@endforeach
	@else
		<div class="text-center alert info">We have no articles right now.</div>
	@endif
	@if(Auth::check())
		@if(HTML::hasPermission('ArticleController@create') || HTML::hasPermission('ArticleController@update') && isset($article->id))
			@if(isset($article->id))
				{{ Form::model($article, array('action' => array('ArticleController@update', $article->id))) }}
				<h3 class="margin-top-one">Update article</h3>
			@else
				{{ Form::model($article, array('action' => 'ArticleController@create')) }}
				<h3 class="margin-top-one">Create article</h3>
			@endif
			{{ Form::label('Title', 'Title:') }}
			{{ Form::text('title', Input::old('title'), array('id' => 'Title', 'placeholder' => 'Title of article', 'data-validator' => 'required|min:3')) }}
			{{ $errors->first('title', '<div class="alert error">:message</div>') }}
			{{ Form::label('Body', 'Body:') }}
			{{ Form::textarea('body', Input::old('body'), array('id' => 'Body', 'placeholder' => 'Body of article', 'data-validator' => 'required|min:3')) }}
			{{ $errors->first('body', '<div class="alert error">:message</div>') }}
			<div class="margin-top-minus-one font-small">You can use {{HTML::actionlink($url = array('action' => 'MenuController@markdown'), 'markdown')}} in article body!</div>
			{{ Form::button('Send', array('type' => 'submit')) }}
			{{ Form::close() }}
		@endif
	@endif
@stop

@section('script')

@stop