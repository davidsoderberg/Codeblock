@if(!empty($doc['methods']))
    <h2>Methods</h2>
    @foreach($doc['methods'] as $key => $property)
        <h3>{{ $key }}</h3>
        <p><b>Modifier:</b> {{$property['modifier']}}</p>
        <p>{{ $property['desc']  }}</p>
        <div class="method">
            @if(!empty($property['params']))
                <div class="params-return">
                    <h4>Params</h4>
                    @foreach($property['params'] as $param)
                        <li>
                            {{$param['type']}} {{$param['var']}} {{$param['desc']}}
                            @if(isset($param['defaultValue']))
                                | <b>Default:</b>
                                @if(is_array($param['defaultValue']))
                                    {{json_encode($param['defaultValue'])}}
                                @elseif($param['defaultValue'] === '')
                                    ""
                                @else
                                    {{$param['defaultValue']}}
                                @endif
                            @endif
                        </li>
                    @endforeach
                </div>
            @endif
            @if(!empty($property['return']))
                <div class="params-return">
                    <h4>Return value</h4> {{$property['return'][0]['type']}} {{$property['return'][0]['desc']}}
                </div>
            @endif
            @if(!empty($property['throws']))
                <div class="clear"></div>
                <div>
                    <h4>Exceptions</h4>
                    @foreach($property['throws'] as $exception)
                        {{$exception}}
                    @endforeach
                </div>
            @endif
        </div>
    @endforeach
@endif