@extends('layouts.minimalLayout')

@section('html_title', 'Sign In')

@section('page_content')

    <div class="form-box" id="login-box">
        <div class="header">SeAT | Sign In</div>

        {{ Form::open(array('action' => 'UserController@postSignIn')) }}

            <div class="body bg-gray">
                <div class="form-group">
                    {{ Form::text('username', null, array('class' => 'form-control', 'placeholder' => 'User ID'), 'required', 'autofocus') }}
                </div>
                <div class="form-group">
                    {{ Form::password('password', array('class' => 'form-control', 'placeholder' => 'Password')) }}
                </div>          
                <div class="form-group">
                    {{ Form::checkbox('remember_me', 'yes') }} Remember me
                </div>
            </div>

            <div class="footer">                                                               
                {{ Form::submit('Sign me in', array('class' => 'btn bg-olive btn-block')) }}
                <p>{{ HTML::linkAction('RemindersController@getRemind', 'I Forgot My Password') }}</p>
                <p>{{ HTML::linkAction('RegisterController@getNew', 'Register a new membership') }}</p>
            </div>
            
        {{ Form::close() }}
    </div>
@stop
