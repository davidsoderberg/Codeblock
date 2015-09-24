<!doctype html>
<html>
<!--<html manifest="{{asset('codeblock.manifest')}}">-->
	<head>
		<meta name="google-site-verification" content="3M7wk4STJBxWp1JZHRFZ-LNG7N8kZkYIRDqX4uRJsLk" />
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>{{$siteName}} @if(isset($title) && $title != '') - {{ $title }} @endif</title>

		<meta property="og:title" content="{{$title or $siteName}}" />
		<meta property="og:type" content="website" />
		<meta property="og:site_name" content="{{$siteName}}"/>
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
					{{HTML::actionlink($url = array('action' => 'NotificationController@listNotification'), Auth::user()->unread->count(), array('id' => 'notficationNumber', 'class' => 'tag'))}}
				@endif
				</div>
				<h1 id="loggo">
					@if(Auth::check())
						{{HTML::actionlink($url = array('action' => 'MenuController@browse'), '<span class="color-red">C</span><span class="color-green">o</span><span class="color-orange">d</span><span class="color-blue">e</span>block<img src="'.HTML::version('img/favicon.png').'" width="32" height="32" alt="">')}}
					@else
						{{HTML::actionlink($url = array('action' => 'MenuController@index'), '<span class="color-red">C</span><span class="color-green">o</span><span class="color-orange">d</span><span class="color-blue">e</span>block<img src="'.HTML::version('img/favicon.png').'" width="32" height="32" alt="">')}}
					@endif
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
			@yield('content')
			<div id="toast-container">{{HTML::toast()}}</div>
		</div>
		<div id="footer">
			<div class="wrapper">
				<div class="float-left">
					Copyright &copy; 2014-{{Date('Y')}} David Söderberg
				</div>
				<div class="menu float-right">
					<ul>
						<li class="divider"></li>
						<li><a href="https://www.facebook.com/Codeblock-1470640509923344/timeline/" target="_blank"><i class="fa fa-facebook-square facebook-blue"></i> Facebook</a></li>
						<li class="divider"></li>
						<li><a href="https://twitter.com/Codeblock_se" target="_blank"><i class="fa fa-twitter-square twitter-blue"></i> Twitter</a></li>
						<li class="divider"></li>
						<li><a href="" target="_blank"><i class="fa fa-google-plus-square google-plus-red"></i> Google+</a></li>
						<li class="divider"></li>
                        <li><a href="https://github.com/davidsoderberg/codeblock" target="_blank"><i class="fa fa-github-square"></i> Github</a></li>
                        <li class="divider"></li>
					</ul>
				</div>
			</div>
		</div>
		<div class="confirmModal">
			<div class="wrapper text-center">
				<h2>Are you sure?</h2>
				<a href="" class="button big green-background">Yes</a>
				<a href="" class="button big red-background">No</a>
			</div>
		</div>
		<script>
			var appConfig = {
				SOCKET_PORT: '{{env('SOCKET_PORT')}}',
				SOCKET_ADRESS: '{{env('SOCKET_ADRESS')}}'
			};
		</script>
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