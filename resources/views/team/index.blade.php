@extends('master')

@section('css')

@stop

@section('content')
    <h2>Teams</h2>
    <div class="verticalRule">
        <div class="float-left">
            <h3>List of teams</h3>
            {{ HTML::table(array('title'), $teams, array('Edit' => 'TeamController@index', 'Delete' => 'TeamController@delete', 'Pagination' => 10), 'There are no teams right now.') }}
        </div>
        <div class="float-right">
            @if(isset($team->id))
                {{ Form::model($team, array('action' => array('TeamController@createOrUpdate', $team->id))) }}
            @else
                {{ Form::model($team, array('action' => 'TeamController@createOrUpdate')) }}
            @endif
            <h3>Make/update</h3>
            {{ Form::label('title', 'Title:') }}
            {{ Form::text('title', Input::old('title'), array('id' => 'title', 'placeholder' => 'Title of team', 'data-validator' => 'required|min:3')) }}
            {{ $errors->first('title', '<div class="alert error">:message</div>') }}
            {{ Form::button('Send', array('type' => 'submit')) }}
            {{ Form::close() }}
        </div>
    </div>
@stop

@section('script')

@stop