@extends('master')

@section('css')

@stop

@section('content')
    <div class="verticalRule">
        <div class="float-left">
            <h2>Login</h2>
            {{ Form::model(null, array('action' => 'UserController@Usersession')) }}
            {{ Form::label('loginUsername', 'Username:') }}
            {{ Form::text('loginUsername', Input::old('loginUsername'), array('id' => 'loginUsername', 'placeholder' => 'Username', 'data-validator' => 'required')) }}
            {{ Form::label('loginPassword', 'Password:') }}
            {{ Form::password('loginpassword', array('id' => 'loginPassword', 'placeholder' => 'Password', 'data-validator' => 'required')) }}
            <div class="clearfix">
                {{ Form::button('Login', array('type' => 'submit')) }}
	            <p class="pull-left">
		            Login with:
		            {{HTML::actionlink($url = array('action' => 'UserController@oauth', 'params' => array('facebook')), '<i class="fa fa-facebook-square facebook-blue"></i>')}}
		            {{HTML::actionlink($url = array('action' => 'UserController@oauth', 'params' => array('twitter')), '<i class="fa fa-twitter-square twitter-blue"></i>')}}
		            {{HTML::actionlink($url = array('action' => 'UserController@oauth', 'params' => array('google')), '<i class="fa fa-google-plus-square google-plus-red"></i>')}}
		            {{HTML::actionlink($url = array('action' => 'UserController@oauth', 'params' => array('bitbucket')), '<i class="fa fa-bitbucket-square bitbucket-blue"></i>')}}
		            {{HTML::actionlink($url = array('action' => 'UserController@oauth', 'params' => array('github')), '<i class="fa fa-github-square github-black"></i>')}}
	            </p>
            </div>
            {{ Form::close() }}
        </div>
        <span class="text">OR</span>

        <div class="horizontalRule only-small"><span>OR</span></div>
        <div class="float-right">
            <h2>Sign up</h2>
            {{ Form::model(null, array('action' => 'UserController@store')) }}
            {{ Form::label('createUsername', 'Username:') }}
            {{ Form::text('username', Input::old('username'), array('id' => 'createUsername', 'placeholder' => 'Username', 'data-validator' => 'required')) }}
            {{ $errors->first('username', '<div class="alert error">:message</div>') }}
            {{ Form::label('createEmail', 'Email:') }}
            {{ Form::text('email', Input::old('email'), array('id' => 'createEmail', 'placeholder' => 'Email', 'data-validator' => 'required|pattern:email')) }}
            {{ $errors->first('email', '<div class="alert error">:message</div>') }}
            {{ Form::label('createPassword', 'Password:') }}
            {{ Form::password('password', array('id' => 'createPassword', 'placeholder' => 'Password', 'data-validator' => 'required')) }}
            {{ $errors->first('password', '<div class="alert error">:message</div>') }}
            <span class="float-left"><a href="" class="toogleModal font-bold">License for
                    codeblock</a></span>
            {{ Form::button('Sign up', array('type' => 'submit')) }}
            {{ Form::close() }}
        </div>
    </div>
    <div class="horizontalRule"><span>OR</span></div>
    <h2>Forgot password?</h2>
    {{ Form::model(null, array('action' => 'UserController@forgotPassword')) }}
    {{ Form::label('forgotEmail', 'Email:') }}
    {{ Form::text('email', Input::old('email'), array('id' => 'forgotEmail', 'placeholder' => 'Email', 'data-validator' => 'required|pattern:email')) }}
    {{ HTML::Honeypot() }}
    {{ Form::button('Send password', array('type' => 'submit')) }}
    {{ Form::close() }}
    @include('partials.licenseModal')
@stop

@section('script')

@stop