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
@if(count($lang) > 1)
	@foreach($lang as $la)
		<script src="{{ asset('js/codemirror/mode/'.$la.'/'.$la.'.js') }}"></script>
	@endforeach
@else
	<script src="{{ asset('js/codemirror/mode/'.$lang.'/'.$lang.'.js') }}"></script>
@endif
</body>
</html>