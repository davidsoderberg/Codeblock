@extends('master')

@section('css')

@stop

@section('content')
	<h2>News articles</h2>
	@foreach($articles as $art)
		<h3 class="margin-top-one">{{$art->title}}</h3>
		<p class="no-margin font-bold">
			{{$art->created_at->diffForHumans()}}
			<span class="float-right">
				{{HTML::actionlink($url = array('action' => 'ArticleController@delete', 'params' => array($art->id)), '<i class="fa fa-trash-o"></i>')}}
				{{HTML::actionlink($url = array('action' => 'ArticleController@index', 'params' => array($art->id)), '<i class="fa fa-pencil"></i>')}}
			</span>
		</p>
		<p>{{$art->body}}</p>
	@endforeach
	@if(HTML::hasPermission('ArticleController@create') || HTML::hasPermission('ArticleController@update') && isset($article->id))
	@if(isset($article->id))
		{{ Form::model($article, array('action' => array('ArticleController@update', $article->id))) }}
		<h3>Update article</h3>
	@else
		{{ Form::model($article, array('action' => 'ArticleController@create')) }}
		<h3>Create article</h3>
	@endif
	{{ Form::label('Title', 'Title:') }}
	{{ Form::text('title', Input::old('title'), array('id' => 'Title', 'placeholder' => 'Title of article', 'data-validator' => 'required|min:3')) }}
	{{ $errors->first('title', '<div class="alert error">:message</div>') }}
	{{ Form::label('Body', 'Body:') }}
	{{ Form::textarea('body', Input::old('body'), array('id' => 'Body', 'placeholder' => 'Body of article', 'data-validator' => 'required|min:3')) }}
	{{ $errors->first('body', '<div class="alert error">:message</div>') }}
	{{ Form::button('Send', array('type' => 'submit')) }}
	{{ Form::close() }}
	@endif
@stop

@section('script')

@stop