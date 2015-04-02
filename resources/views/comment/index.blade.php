@extends('master')

@section('css')

@stop

@section('content')
    <h2>Your codeblocks comments</h2>
    @if(count($comments) > 0)
        <table>
            <thead>
            <tr>
                <th>Made by</th>
                <th>On</th>
                <th>Comment</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($comments as $comment)
                <tr>
                    <td data-title="Made by">
	                    {{HTML::actionlink($url = array('action' => 'UserController@show', 'params' => array($comment->user_id)), $comment->user->username)}}
                    </td>
                    <td data-title="On">
	                    {{HTML::actionlink($url = array('action' => 'PostController@show', 'params' => array($comment->post_id)), $comment->post->name)}}
                    </td>
                    <td data-title="Comment">{{ $comment->comment}}</td>
                    <td data-title="Status">@if($comment->status == 0 ) Hidden @else Shown @endif</td>
                    <td data-title="Actions">
	                    {{HTML::actionlink($url = array('action' => 'CommentController@delete', 'params' => array($comment->id)), '<i class="fa fa-trash-o"></i>Delete')}}
	                    {{HTML::actionlink($url = array('action' => 'CommentController@edit', 'params' => array($comment->id)), '<i class="fa fa-pencil"></i>Edit')}}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @else
        <div class="text-center alert info">You have no comments on your codeblocks yet.</div>
    @endif
@stop

@section('script')

@stop