@extends('master')

@section('css')

@stop

@section('content')
    <h2>Codeblocks comments</h2>
    {{ HTML::table(array(array('Made by' => 'userlink'), array('on' => 'postlink'), 'comment'), $comments, array('Pagination' => 10,  'Edit' => 'CommentController@edit', 'Delete' => 'CommentController@delete'), 'You have not done any comments yet.') }}
@stop

@section('script')

@stop