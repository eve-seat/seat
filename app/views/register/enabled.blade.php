@extends('layouts.minimalLayout')

@section('page_content')

  <div class="form-box" id="login-box">
    <div class="header">{{ \App\Services\Settings\SettingHelper::getSetting('app_name') }} | Register</div>

    {{ Form::open(array('action' => 'RegisterController@postNew')) }}

      <div class="body bg-gray">
        <div class="form-group">
          {{ Form::text('username', null, array('class' => 'form-control', 'placeholder' => 'Username'), 'required', 'autofocus') }}
        </div>
        <div class="form-group">
          {{ Form::text('email', null, array('class' => 'form-control', 'placeholder' => 'Email Address'), 'required', 'autofocus') }}
        </div>
        <div class="form-group">
          {{ Form::password('password', array('class' => 'form-control', 'placeholder' => 'Password'), 'required') }}
        </div>
        <div class="form-group">
          {{ Form::password('password_confirmation', array('class' => 'form-control', 'placeholder' => 'Password Again'), 'required') }}
        </div>
      </div>

      <div class="footer">
        {{ Form::submit('Send My Activation Link', array('class' => 'btn bg-olive btn-block')) }}
        <a href="{{ URL::previous() }}">Back to Main Menu</a>
      </div>

    {{ Form::close() }}

  </div>

@stop
