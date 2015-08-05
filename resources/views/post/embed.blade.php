<html>
<head>
	<link rel="stylesheet" href="{{HTML::version('css/style.css')}}">
</head>
<body id="embed">
<h2>
	{{HTML::actionlink($url = array('action' => 'PostController@show', 'params' => array($post->slug)), $post->name, array('target' => '_blank'))}}
</h2>
@if(isset($post->category->name))
	<textarea class="code-editor readonly" data-lang="{{ strtolower($post->category->name) }}"
	          id="blockCode">{{ $post->code }}</textarea>
@else
	<textarea class="code-editor readonly" data-lang="xml" id="blockCode">{{ $post->code }}</textarea>
@endif
<script src="{{ HTML::version('js/script.min.js') }}"></script>
{{HTML::codemirror($post->category->name)}}
</body>
</html>