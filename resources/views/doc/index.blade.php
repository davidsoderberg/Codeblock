@include('doc.header')
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
@include('doc.footer')
</body>
</html>