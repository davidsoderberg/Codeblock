<h1>{{$doc['name']}}</h1>
<p class="font-bold">
    @if($doc['isAbstract'])
        abstract
    @elseif($doc['isFinal'])
        final
    @endif
    @if($doc['isInterface'])
        interface
    @elseif($doc['isTrait'])
        trait
    @else
        class
    @endif
    {{$doc['name']}}
</p>
<p><b>Namespace:</b> {{$doc['namespace']}}</p>
@if(isset($doc['tags']))
    <p>
        @foreach($doc['tags'] as $key => $tag)
            <b>{{$key}}:</b> {{ implode('', $tag) }}
        @endforeach
    </p>
@endif
@if(isset($doc['desc']))
    <p><b>Description:</b> {{$doc['desc']}}</p>
@endif
@if(!empty($doc['interfaces']))
    <h2>Interfaces</h2>
    @foreach($doc['interfaces'] as $interface)
        <p>{{$interface}}</p>
    @endforeach
@endif
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