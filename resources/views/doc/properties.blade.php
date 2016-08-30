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
        <p><b>Modifier:</b> {{$property['modifier']}}</p>
        <p>{{ $property['desc']  }}</p>
        <br>
    @endforeach
@endif