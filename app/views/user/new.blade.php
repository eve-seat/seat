@extends('layouts.masterLayout')
@section('html_title', 'New User')

@section('page_content')

   <div class="box">
    <div class="box-header">
        <h3 class="box-title">Add New User</h3>
    </div>

      <div class="box-body table-responsive">

      {{ Form::open(array('action' => 'UserController@postNewUser', 'class' => 'form-horizontal')) }}
        <fieldset>

        <div class="form-group">
          <label class="col-md-4 control-label" for="email">Email Address</label>
          <div class="col-md-4">
            <div class="input-group">
              <span class="input-group-addon"><i class="fa fa-lock"></i></span>
              {{ Form::text('email', null, array('id' => 'email', 'class' => 'form-control', 'placeholder' => 'Email Address'), 'required', 'autofocus') }}
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

        <div class="form-group">
          <label class="col-md-4 control-label" for="first_name">First Name</label>
          <div class="col-md-4">
            <div class="input-group">
              <span class="input-group-addon"><i class="fa fa-user"></i></span>
              {{ Form::text('first_name', null, array('id' => 'first_name', 'class' => ' form-control', 'placeholder' => 'First Name')) }}
            </div>
          </div>
        </div>

        <div class="form-group">
          <label class="col-md-4 control-label" for="last_name">Last Name</label>
          <div class="col-md-4">
            <div class="input-group">
              <span class="input-group-addon"><i class="fa fa-user"></i></span>
              {{ Form::text('last_name', null, array('id' => 'last_name', 'class' => ' form-control', 'placeholder' => 'Last Name')) }}
            </div>
          </div>
        </div>

        <div class="form-group">
          <label class="col-md-4 control-label" for="is_admin">Administrator</label>
          <div class="col-md-4">
            <div class="input-group">
              <span class="input-group-addon">{{ Form::checkbox('is_admin', 'yes') }}</span>
              {{ Form::text('label', null, array('id' => 'last_name', 'class' => ' form-control', 'placeholder' => 'Check to make this user an administrator', 'disabled' => 'disabled')) }}
            </div>
          </div>
        </div>


        <!-- Button -->
        <div class="form-group">
          <label class="col-md-4 control-label" for="singlebutton"></label>
          <div class="col-md-4">
            {{ Form::submit('Add User', array('class' => 'btn bg-olive btn-block')) }}            
          </div>
        </div>

        </fieldset>
      {{ Form::close() }}

      </div><!-- /.box-body -->
  </div><!-- /.box -->

@stop