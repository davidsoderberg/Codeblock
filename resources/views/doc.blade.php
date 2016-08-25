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
            <span class="color-red">C</span><span class="color-green">o</span><span class="color-orange">d</span><span class="color-blue">e</span>block<img src="{{HTML::version('img/favicon.png')}}" width="32" height="32" alt="">
            </a>
        </h1>
        <div class="clear"></div>
    </div>
</div>
<div id="documentation">
    <div class="wrapper">
        <ul>
            @foreach($docs as $key => $doc)
                <li class="@if($loop->first) active @endif">
                    <a href="#{{str_replace('\\', '', $key)}}">{{$key}}</a>
                </li>
            @endforeach
        </ul>
    </div>
    <div class="wrapper">
        @foreach($docs as $key => $doc)
            <div class="content @if($loop->first) show @endif" id="{{str_replace('\\', '', $key)}}">
                <h1>{{$key}}</h1>
                @if(!is_null($doc['tags']))
                    <p>
                        @foreach($doc['tags'] as $key => $tag)
                            <b>Namespace:</b> {{ implode('', $tag) }}
                        @endforeach
                    </p>
                @endif
                <p>{{$doc['desc']}}</p>
                @if(!empty($doc['traits']))
                    <h2>Traits</h2>
                    @foreach($doc['traits'] as $trait)
                        @if(in_array($trait, array_keys($docs)))
                            <p><a href="#{{str_replace('\\', '', $trait)}}">{{$trait}}</a></p>
                        @else
                            <p>{{$trait}}</p>
                        @endif
                    @endforeach
                @endif
                @if(!empty($doc['properties']))
                    <h2>Properties</h2>
                    @foreach($doc['properties'] as $key => $property)
                        <p><b>{{ '$' . $key }}</b>
                            @if(!is_null($property['tags']))
                                (
                                @foreach($property['tags'] as $key => $tag)
                                    {{$key}} {{ implode('', $tag) }}
                                @endforeach
                                )
                            @endif
                        </p>
                        <p>{{ $property['desc']  }}</p>
                        <br>
                    @endforeach
                @endif
                @if(!empty($doc['methods']))
                    <h2>Methods</h2>
                    @foreach($doc['methods'] as $key => $property)
                        <h3>{{ $key }}</h3>
                        <p>{{ $property['desc']  }}</p>
                        <div class="method">
                            @if(!is_null($property['tags']))
                                @foreach($property['tags'] as $key => $tag)
                                    @if($key == 'param')
                                        <div class="params-return">
                                            <h4>Params</h4>
                                            @foreach($tag as $param)
                                                @if(!$loop->last)
                                                    <li>{{$param['type']}} {{$param['var']}} {{$param['desc']}}</li>
                                                @else
                                                    <li>{{$param['type']}} {{$param['var']}} {{$param['desc']}}</li>
                                                @endif
                                            @endforeach
                                        </div>
                                    @elseif($key == 'return')
                                        <div class="params-return">
                                            <h4>Return value</h4> {{$tag[0]['type']}} {{$tag[0]['desc']}}
                                        </div>
                                    @elseif($key == 'throws')
                                        <div class="clear"></div>
                                        <div>
                                            <h4>Exceptions</h4>
                                            @foreach($tag as $exception)
                                                {{$exception}}
                                            @endforeach
                                        </div>
                                    @endif
                                @endforeach
                            @endif
                        </div>
                    @endforeach
                @endif
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