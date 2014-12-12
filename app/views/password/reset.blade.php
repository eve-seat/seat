@extends('layouts.minimalLayout')

@section('html_title', 'Password Reset')

@section('page_content')

  <div class="form-box" id="login-box">
    <div class="header">SeAT | Password Reset</div>

      {{ Form::open(array('action' => 'RemindersController@postReset')) }}

        <div class="body bg-gray">
          <p>
            Please fill out the below form to reset your password. For security reasons, please ensure that you enter the email address
            that this link was sent to originally as well.
          </p>
          <p class="text-red">Passwords must be at least six (6) characters long</p>

          {{ Form::hidden('token', $token) }}

          <div class="form-group">
            {{ Form::text('email', null, array('class' => 'form-control', 'placeholder' => 'Email Address'), 'required', 'autofocus') }}
          </div>
          <div class="form-group">
            {{ Form::password('password', array('class' => 'form-control', 'placeholder' => 'Password')) }}
          </div>
          <div class="form-group">
            {{ Form::password('password_confirmation', array('class' => 'form-control', 'placeholder' => 'Password Confirmation')) }}
          </div>
        </div>

        <div class="footer">
          {{ Form::submit('Reset My Password', array('class' => 'btn bg-olive btn-block')) }}
          <p><a href={{ url('/')}} >Go home</a></p>
        </div>

        {{ Form::close() }}

    </div>

@stop
