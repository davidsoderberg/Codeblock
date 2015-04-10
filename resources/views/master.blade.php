<!doctype html>
<html>
	<head>
		<meta name="google-site-verification" content="3M7wk4STJBxWp1JZHRFZ-LNG7N8kZkYIRDqX4uRJsLk" />
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Codeblock.se @if(isset($title) && $title != '') - {{ $title }} @endif</title>

		<meta property="og:title" content="{{$title or 'Codeblock.se'}}" />
		<meta property="og:type" content="website" />
		<meta property="og:site_name" content="Codeblock.se"/>
		<meta property="og:url" content="{{ Request::url() }}" />
		<meta property="og:image" content="{{ HTML::version('img/favicon.png') }}" />
		<meta property="og:description" content="Share your commonly used codeblocks and use the community contributions for fast web development. Syntax support for commonly used web development languages.">

		<link rel="icon" type="image/png" href="{{ HTML::version('img/favicon.png') }}">
		@yield('css')
		<link rel="stylesheet" href="{{HTML::version('css/style.css')}}">
	</head>
	<body>
		<div class="menu-wrap full-width">
			<div class="wrapper">
				<a href="" class="close-button">X</a>
				@include('partials.headerMenu')
			</div>
		</div>
		<div id="header">
			<div class="wrapper">
				<div class="menu-button">
				<a href=""><i class="fa fa-3x fa-bars"></i></a>
				@if(Auth::check() && Auth::user()->unread->count() > 0)
					<a href="/notifications" id="notficationNumber" class="tag">{{Auth::user()->unread->count()}}</a>
				@endif
				</div>
				<h1 id="loggo">
					<a href="/">
						<span class="color-red">C</span><span class="color-green">o</span><span class="color-orange">d</span><span class="color-blue">e</span>block<img src="{{ HTML::version('img/favicon.png') }}" width="32" height="32" alt="">
					</a>
				</h1>
				<div class="display-none">
					<a href="#" id="menubutton" class="hideUl"><i class="fa fa-bars"></i>Menu</a>
					<div class="menu">
						@include('partials.headerMenu')
					</div>
				</div>
				<div class="clear"></div>
			</div>
		</div>
		<div class="wrapper">
            @include('partials.modal')
			{{ HTML::flash() }}
			@yield('content')
			<div id="toast-container"></div>
		</div>
		<div id="footer">
			<div class="wrapper">
				<div class="float-left">
					Copyright &copy; 2014-{{Date('Y')}} David Söderberg
				</div>
				<div class="menu float-right">
					<ul>
						<li class="divider"></li>
						<li><a href="" target="_blank"><i class="fa fa-facebook-square facebook-blue"></i> Facebook</a></li>
						<li class="divider"></li>
						<li><a href="" target="_blank"><i class="fa fa-twitter-square twitter-blue"></i> Twitter</a></li>
						<li class="divider"></li>
						<li><a href="" target="_blank"><i class="fa fa-google-plus-square google-plus-red"></i> Google+</a></li>
						<li class="divider"></li>
                        <li><a href="https://github.com/davidsoderberg/codeblock" target="_blank"><i class="fa fa-github-square"></i> Github</a></li>
                        <li class="divider"></li>
					</ul>
				</div>
			</div>
		</div>
		<script src="{{ HTML::version('js/script.min.js') }}"></script>
        @yield('script')
		<script>
			(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
			(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
			m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
			})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
			ga('create', 'UA-51698881-1', 'codeblock.se');
			ga('send', 'pageview');
		</script>
	</body>
</html>