@extends('master')

@section('css')
@stop

@section('content')
<h2>Welcome</h2>
<textarea class="code-editor readonly" data-lang="html" id="blockCode" name="code">
&lt;p&gt;Welcome to codeblock.se, a website where you can share your codeblocks that you use over and over again.&lt;/p&gt;
&lt;p&gt;Browse others codeblock or sign up and add your own or comment on another codeblocks.&lt;/p&gt;
&lt;p&gt;
	&lt;strong&gt;
		Welcome/&lt;br&gt;
		David Southmountain
	&lt;/strong&gt;
&lt;/p&gt;</textarea>
<div class="text-center">
	{{HTML::actionlink($url = array('action' => 'MenuController@browse'), 'START BROWSING CODE', array('class' => 'button big'))}}
</div>
@stop

@section('script')
	<script src="{{ asset('/js/codemirror/mode/xml/xml.js') }}"></script>
@stop