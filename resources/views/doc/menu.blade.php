<div class="wrapper">
    <input id="filter" type="text" placeholder="Filter">
    <ul>
        @foreach($docs as $key => $doc)
            <li class="@if($loop->first) active @endif">
                <a href="#{{str_replace('\\', '', $key)}}">{{$key}}</a>
                @if($doc['isInterface'])<span class="float-right">(I)</span>@endif
                @if($doc['isAbstract'])<span class="float-right">(A)</span>@endif
                @if($doc['isFinal'])<span class="float-right">(F)</span>@endif
                @if($doc['isTrait'])<span class="float-right">(T)</span>@endif
            </li>
        @endforeach
    </ul>
</div>