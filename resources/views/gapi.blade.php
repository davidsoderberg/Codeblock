@extends('master')

@section('css')

@stop

@section('content')
	<h2>{{ $title }}</h2>
	<div id="myStocks"></div>
@stop

@section('script')
	{{Lava::render($chart, $table, 'myStocks'); }}
@stop