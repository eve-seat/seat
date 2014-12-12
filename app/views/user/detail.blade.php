@extends('layouts.masterLayout')

@section('html_title', 'User Details')

@section('page_content')

  <div class="box">
    <div class="box-header">
      <h3 class="box-title">User Details</h3>
    </div>

    <div class="box-body table-responsive">

      {{ Form::open(array('action' => 'UserController@postUpdateUser', 'class' => 'form-horizontal')) }}
        {{ Form::hidden('userID', $user->getKey()) }}
        <fieldset>

          <div class="form-group">
            <label class="col-md-4 control-label" for="email">Email Address</label>
            <div class="col-md-4">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                {{ Form::text('email', $user->email, array('id' => 'email', 'class' => 'form-control', 'placeholder' => 'Email Address'), 'required', 'autofocus') }}
              </div>
            </div>
          </div>

          <div class="form-group">
            <label class="col-md-4 control-label" for="username">Username</label>
            <div class="col-md-4">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                {{ Form::text('username', $user->username, array('id' => 'email', 'class' => 'form-control', 'placeholder' => 'Username'), 'required') }}
              </div>
            </div>
          </div>

          <div class="form-group">
            <label class="col-md-4 control-label" for="password">Password</label>
            <div class="col-md-4">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-magic"></i></span>
                {{ Form::password('password', array('id' => 'password', 'class' => ' form-control', 'placeholder' => 'Password'), 'required') }}
              </div>
            </div>
          </div>

          @foreach ($availableGroups as $availableGroup)

            <div class="form-group">
              <label class="col-md-6 control-label" for="singlebutton">{{ $availableGroup->name }}</label>
              <div class="form-group">
                {{ Form::checkbox($availableGroup->name, '1', (isset($hasGroups[$availableGroup->name]) ? true : false)) }}
              </div>
            </div>

          @endforeach

          <!-- Button -->
          <div class="form-group">
            <label class="col-md-4 control-label" for="singlebutton"></label>
            <div class="col-md-4">
              {{ Form::submit('Update User', array('class' => 'btn bg-olive btn-block')) }}
            </div>
          </div>

        </fieldset>
      {{ Form::close() }}

    </div><!-- /.box-body -->
  </div><!-- /.box -->

@stop
