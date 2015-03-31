@extends('master')

@section('css')
@stop

@section('content')
	<h2>Create Codeblock</h2>
	@if($hasRequest)
		{{ Form::model(null, array('action' => 'PostController@forkGist')) }}
			{{ Form::label('gistId', 'Id of gist:') }}
			{{ Form::text('id', Input::old('id'), array('id' => 'gistId', 'placeholder' => 'Id of gist', 'data-validator' => 'required')) }}
			{{ Form::button('Fork gist', array('type' => 'submit')) }}
		{{ Form::close() }}
		<div class="horizontalRule"><span>OR</span></div>
	@endif
	{{ Form::model($post, array('action' => 'PostController@createOrUpdate')) }}
		{{ Form::label('blockName', 'Name:') }}
		{{ Form::text('name', Input::old('name'), array('id' => 'blockName', 'placeholder' => 'Name of codeblock', 'data-validator' => 'required|min:3')) }}
		{{ $errors->first('name', '<div class="alert error">:message</div>') }}
		<div class="verticalRule">
			<div class="float-left">
				{{ Form::label('blockCategory', 'Category:') }}
				{{ Form::select('category', $categories, '', array('id' => 'blockCategory', 'data-validator' => 'required')) }}
				{{ $errors->first('category', '<div class="alert error">:message</div>') }}
			</div>
			<div class="float-right">
				{{ Form::label('private', 'Private codeblock:') }}<br />
				{{ Form::select('private', array(0 => 'No', 1 => 'Yes'), 0) }}
			</div>
		</div>
		{{ Form::label('', 'Tags:') }}
		{{ Form::select('tags[]', $tags, '', array('multiple', 'class' => 'chosen-select', 'data-placeholder' => 'Choose some tags')) }}

		{{ Form::label('blockCode', 'Code:') }}
		{{ Form::textarea('code', Input::old('code'), array('class'=> 'code-editor', 'data-lang' => 'php' ,'id' => 'blockCode', 'placeholder' => 'Code goes here...', 'data-validator' => 'required|min:3')) }}

		{{ Form::label('blockDescription', 'Description:', array('class' =>'margin-top-one display-block')) }}
		{{ Form::textarea('description', Input::old('description'), array('id' => 'blockDescription', 'rows' => '2', 'placeholder' => 'Description of codeblock', 'data-validator' => 'required|min:3')) }}
		{{ $errors->first('description', '<div class="alert error">:message</div>') }}

		{{ Form::button('Create', array('type' => 'submit')) }}
	{{ Form::close() }}
@stop

@section('script')
	<script src="{{ asset('js/codemirror/addon/display/placeholder.js') }}"></script>
	@foreach ($categories as $key => $category)
		@if($key != '')
			@if($category == 'Html')
				<script src="{{ asset('js/codemirror/mode/xml/xml.js') }}"></script>
			@elseif(strtolower($category) == 'c#' || strtolower($category) == 'asp.net')
				<script src="{{ asset('js/codemirror/mode/clike/clike.js') }}"></script>
			@else
				<script src="{{ asset('js/codemirror/mode/'.strtolower($category).'/'.strtolower($category).'.js') }}"></script>
			@endif
		@endif
	@endforeach
@stop