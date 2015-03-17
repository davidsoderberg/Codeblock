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
                    <td data-title="Made by"><a href="/user/{{ $comment->user_id }}">{{ $comment->user->username }}</a>
                    </td>
                    <td data-title="On"><a href="/posts/{{ $comment->post_id }}">{{ $comment->post->name }}</a></td>
                    <td data-title="Comment">{{ $comment->comment}}</td>
                    <td data-title="Status">@if($comment->status == 0 ) Hidden @else Shown @endif</td>
                    <td data-title="Actions">
                        <a href="/comments/delete/{{ $comment->id }}"><i class="fa fa-trash-o"></i>Delete</a>
                        <a href="/comments/edit/{{ $comment->id }}"><i class="fa fa-pencil"></i>Edit</a>
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