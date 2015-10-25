@extends('master')

@section('css')
@stop

@section('content')
	<h2>Create Codeblock</h2>
	{{ Form::model($post, array('action' => 'PostController@createOrUpdate')) }}
		{{ Form::label('blockName', 'Name:') }}
		{{ Form::text('name', Input::old('name'), array('id' => 'blockName', 'placeholder' => 'Name of codeblock', 'data-validator' => 'required|min:3')) }}
		{{ $errors->first('name', '<div class="alert error">:message</div>') }}
		<div class="verticalRule">
			<div class="float-left">
				{{ Form::label('blockCategory', 'Category:') }}
				{{ Form::select('cat_id', $categories, '', array('id' => 'blockCategory', 'data-validator' => 'required', 'data-name' => 'category')) }}
				{{ $errors->first('cat_id', '<div class="alert error">:message</div>') }}
			</div>
			<div class="float-right">
				{{ Form::label('private', 'Private codeblock:') }}<br />
				{{ Form::select('private', array(0 => 'No', 1 => 'Yes'), 0) }}
			</div>
		</div>
		<div class="verticalRule">
			<div class="float-left">
				{{ Form::label('', 'Tags:') }}
				{{ Form::select('tags[]', $tags, '', array('multiple', 'class' => 'chosen-select', 'data-placeholder' => 'Choose some tags')) }}
			</div>
			<div class="float-right">
				{{ Form::label('team_id', 'Codeblock belongs to team:') }}<br />
				{{ Form::select('team_id', $teams, 0) }}
			</div>
		</div>

		{{ Form::label('blockCode', 'Code:') }}
		{{ Form::textarea('code', Input::old('code'), array('class'=> 'code-editor', 'data-lang' => 'php' ,'id' => 'blockCode', 'placeholder' => 'Code goes here...', 'data-validator' => 'required|min:3')) }}
		{{ $errors->first('code', '<div class="alert error">:message</div>') }}

		{{ Form::label('blockDescription', 'Description:', array('class' =>'margin-top-one display-block')) }}
		{{ Form::textarea('description', Input::old('description'), array('id' => 'blockDescription', 'rows' => '2', 'placeholder' => 'Description of codeblock', 'data-validator' => 'required|min:3')) }}
		{{ $errors->first('description', '<div class="alert error">:message</div>') }}

		{{ Form::button('Create', array('type' => 'submit')) }}
	{{ Form::close() }}
	@if($hasRequest)
		<div class="horizontalRule"><span>OR</span></div>
		<h2>Import gist from github</h2>
		{{ Form::model(null, array('action' => 'PostController@forkGist')) }}
			{{ Form::label('title','Gist:') }}
			<div class="input-group">
				{{ Form::text('id', Input::old('id'), array('id' => 'gistId', 'placeholder' => 'Gist id', 'data-validator' => 'required')) }}
				<div class="font-small">Gist is githubs codeblock! You can copy that code to codeblock by copy the id of that code that is shown in the url.</div>
					<span class="button-group">
						{{ Form::button('Fork gist', array('type' => 'submit')) }}
					</span>
			</div>
		{{ Form::close() }}
	@endif
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