<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Codeblock Documentation</title>

    <meta property="og:title" content="Codeblock"/>
    <meta property="og:type" content="website"/>
    <meta property="og:site_name" content="Codeblock"/>
    <meta property="og:url" content="{{ Request::url() }}"/>
    <meta property="og:image" content="{{ HTML::version('img/favicon.png') }}"/>
    <meta property="og:description" content="Documentation for Codeblock">

    <link rel="icon" type="image/png" href="{{ HTML::version('img/favicon.png') }}">
    <link rel="stylesheet" href="{{HTML::version('css/style.css')}}">
</head>
<body>
<div id="header">
    <div class="wrapper">
        <h1 id="loggo">
            <a href="">
                <span class="color-red">C</span><span class="color-green">o</span><span
                    class="color-orange">d</span><span class="color-blue">e</span>block<img
                    src="{{HTML::version('img/favicon.png')}}" width="32" height="32" alt="">
            </a>
        </h1>
        <div class="clear"></div>
    </div>
</div>
<div id="documentation">
    @include('doc.menu')
    <div class="wrapper">
        @foreach($docs as $key => $doc)
            <div class="content @if($loop->first) show @endif" id="{{str_replace('\\', '', $key)}}">
                @include('doc.class')
                @include('doc.properties')
                @include('doc.methods')
            </div>
        @endforeach
    </div>
</div>
<div id="footer">
    <div class="wrapper">
        <div class="text-center">
            Copyright &copy; 2014-{{Date('Y')}} David Söderberg
        </div>
    </div>
</div>
<script>
    var appConfig = {};
</script>
<script src="{{ HTML::version('js/script.min.js') }}"></script>
</body>
</html>