@extends('master')

@section('css')

@stop

@section('content')
    <h2>User: {{ $user->username }}</h2>
    <div class="verticalRule">
        <div class="float-left clearfix">
            @if($user->id == Auth::user()->id)
                <h3>Your codeblocks</h3>
            @else
                <h3>{{ $user->username }}s codeblock</h3>
            @endif
            @if(count($user->posts) != 0)
                @for ($i = 0; $i < 10 ; $i++)
                    @if(isset($user->posts[$i]))
                        @if($user->id == Auth::user()->id || $user->id != Auth::user()->id && $user->posts[$i]->private == 0)
                            <div class="clearfix margin-bottom-half">
							<span class="float-left">
								<a href="/posts/{{ $user->posts[$i]->id }}">{{ $user->posts[$i]->name }}</a>
							</span>
                                @if($user->id == Auth::user()->id)
                                    <span class="float-right">
									<a href="/posts/edit/{{ $user->posts[$i]->id }}"><i class="fa fa-pencil"></i></a>
									<a href="/posts/delete/{{ $user->posts[$i]->id }}"><i class="fa fa-trash-o"></i></a>
								</span>
                                @endif
                            </div>
                        @endif
                    @endif
                @endfor
            @else
                <div class="text-center alert info">No codeblocks, yet</div>
            @endif
            <div class="text-center margin-top-one">
                @if(count($user->posts) > 10)
                    @if($user->id == Auth::user()->id)
                        <a href="/user/list/{{ $user->id }}" class="button float-left">List all</a>
                    @else
                        <a href="/user/list/{{ $user->id }}" class="button float-left">List all {{ $user->username }}s
                            codeblock</a>
                    @endif
                    @if($user->id == Auth::user()->id)
                        <a href="/posts/create" class="button">Create Codeblock</a>
                    @endif
                @else
                    @if($user->id == Auth::user()->id)
                        <a href="/posts/create" class="button float-left">Create Codeblock</a>
                    @endif
                @endif
            </div>
        </div>
        <div class="float-right clearfix margin-top-one">
            <h3>Starred codeblock</h3>
            @if($user->posts->stars > 0)
                @foreach ($user->posts as $post)
                    @if($post->stars > 0)
                        <div class="clearfix margin-bottom-half">
					<span class="float-left">
						<a href="/posts/{{ $post->id }}">{{ $post->name }}, {{ $post->category->name }}</a>
					</span>
					<span class="float-right">
						<i class="fa fa-star"></i> {{ $post->stars }}
					</span>
                        </div>
                    @endif
                @endforeach
            @else
                <div class="text-center alert info">No starred codeblocks, yet</div>
            @endif
        </div>
    </div>
    @if($user->id == Auth::user()->id)
        <h2>Change user information</h2>
        {{ Form::model($user, array('action' => array('UserController@store', $user->id))) }}
        @if(count(Auth::user()->socials) < 5)
            <p class="font-bold">Connect:</p>
            <p class="margin-bottom-one">
                @if(!Auth::user()->hasSocial('facebook'))
                    <a href="/oauth/facebook"><i class="fa fa-15x fa-facebook-square facebook-blue"></i></a>
                @endif
                @if(!Auth::user()->hasSocial('twitter'))
                    <a href="/oauth/twitter"><i class="fa fa-15x fa-twitter-square twitter-blue"></i></a>
                @endif
                @if(!Auth::user()->hasSocial('google'))
                    <a href="/oauth/google"><i class="fa fa-15x fa-google-plus-square google-plus-red"></i></a>
                @endif
                @if(!Auth::user()->hasSocial('bitbucket'))
                    <a href="/oauth/bitbucket"><i class="fa fa-15x fa-bitbucket-square bitbucket-blue"></i></a>
                @endif
                @if(!Auth::user()->hasSocial('github'))
                    <a href="/oauth/github"><i class="fa fa-15x fa-github-square github-black"></i></a>
                @endif
            </p>
        @endif
        {{ Form::label('createEmail', 'Email:') }}
        {{ Form::text('email', Input::old('email'), array('id' => 'createEmail', 'placeholder' => 'Email', 'data-validator' => 'required|pattern:email')) }}
        {{ $errors->first('email', '<div class="alert error">:message</div>') }}
        {{ Form::label('oldPassword', 'Old Password:') }}
        {{ Form::password('oldpassword', array('id' => 'oldPassword', 'placeholder' => 'Old Password')) }}
        {{ $errors->first('oldpassword', '<div class="alert error">:message</div>') }}
        {{ Form::label('createPassword', 'Password:') }}
        {{ Form::password('password', array('id' => 'createPassword', 'placeholder' => 'Password')) }}
        {{ $errors->first('password', '<div class="alert error">:message</div>') }}
        {{ Form::button('Change', array('type' => 'submit')) }}
        {{ Form::close() }}
    @endif
@stop

@section('script')

@stop