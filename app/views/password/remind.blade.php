@extends('layouts.minimalLayout')

@section('html_title', 'Password Reset')

@section('page_content')

  <div class="form-box" id="login-box">
    <div class="header">SeAT | Password Reset</div>

      {{ Form::open(array('action' => 'RemindersController@postRemind')) }}

        <div class="body bg-gray">
          <p>Please enter your email address to receive a password reset link.</p>
          <div class="form-group">
            {{ Form::text('email', null, array('class' => 'form-control', 'placeholder' => 'Email Address'), 'required', 'autofocus') }}
          </div>

        </div>

        <div class="footer">
          {{ Form::submit('Send Password Reset Link', array('class' => 'btn bg-olive btn-block')) }}
          <p><a href={{ url('/')}} >Go home</a></p>
        </div>

      {{ Form::close() }}
    </div>

@stop
