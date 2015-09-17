@extends('master')

@section('css')

@stop

@section('content')
    <h2>Codeblock log</h2>
    @foreach($logs as $log)
        <div class="margin-bottom-one margin-top-one log-{{$log['level']}}">
            <h3 class="text-left">{{$log['level']}}: {{$log['date']}}</h3>
            {{$log['text']}}<br>{{$log['in_file']}}}
        </div>
    @endforeach
@stop

@section('script')

@stop