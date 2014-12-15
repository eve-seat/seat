@extends('layouts.masterLayout')

@section('html_title', 'User Profile')

@section('page_content')

  <div class="row">

    <!-- user details panel -->
    <div class="col-md-6">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">
            <b>Change Profile Settings</b>
          </h3>
        </div>
        <div class="panel-body">

          {{ Form::open(array('action' => 'ProfileController@postSetSettings', 'class' => 'form-horizontal')) }}
          <fieldset>

          <!-- Form Name -->

          <!-- Select Basic -->
          <div class="form-group">
            <label class="col-md-4 control-label" for="selectbasic">Current Main Character</label>
            <div class="col-md-4">
              {{ Form::select('main_character_id', $available_characters, \App\Services\Settings\SettingHelper::getSetting('main_character_id'), array('class' => 'form-control')) }}
            </div>
          </div>

          <!-- Select Basic -->
          <div class="form-group">
            <label class="col-md-4 control-label" for="selectbasic">SeAT Theme</label>
            <div class="col-md-4">
              {{ Form::select('color_scheme', array('blue' => 'Blue', 'black' => 'Black'), \App\Services\Settings\SettingHelper::getSetting('color_scheme'), array('class' => 'form-control')) }}
            </div>
          </div>

          <!-- Button -->
          <div class="form-group">
            <label class="col-md-4 control-label" for="singlebutton"></label>
            <div class="col-md-4">
              <button id="singlebutton" name="singlebutton" class="btn btn-primary">Save</button>
            </div>
          </div>

          </fieldset>
          </form>

        </div>

        <div class="panel-footer">
          {{ $key_count }} Owned API Keys
          <span class="pull-right">

            @if (Auth::isSuperUser())
              <span class="label label-danger">Administrator Account</span>
            @endif

          </span>
        </div>
      </div>
    </div>

    <!-- user details panel -->
    <div class="col-md-6">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">
            <b>{{ $user->username }} ({{ $user->email }})</b>
            <span class="pull-right">
              Last Login: {{ $user->last_login }} ({{ Carbon\Carbon::parse($user->last_login)->diffForHumans() }})
            </span>
          </h3>
        </div>
        <div class="panel-body">

          <div class="col-md-6">
            <p class="lead small">Account Settings</p>
            <a data-toggle="modal" data-target="#password-modal"><i class="fa fa-lock"></i> Change Password</a><br>
            <a data-toggle="modal" data-target="#access-log-modal" id="access-log"><i class="fa fa-th-list"></i> View Account Access Log</a>
          </div>

          <div class="col-md-6">
            <p class="lead small">Group Memberships</p>

              @foreach($groups as $group)

                <i class="fa fa-fw fa-group"></i>{{ $group->name }}<br>

              @endforeach

          </div>
        </div>

        <div class="panel-footer">
          {{ $key_count }} Owned API Keys
          <span class="pull-right">

            @if (Auth::isSuperUser())
              <span class="label label-danger">Administrator Account</span>
            @endif

          </span>
        </div>
      </div>
    </div>

  </div>

  <div class="row">
    <div class="col-md-12">
      <p class="text-center">For any account related enquiries, including permissions amendments, please contact the SeAT administrator.</p>
    </div>
  </div>

  <!-- password reveal modal -->
  <div class="modal fade" id="password-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title"><i class="fa fa-lock"></i> Change Password</h4>
        </div>
        <div class="modal-body">
          <p class="text-center">
            {{ Form::open(array('action' => 'ProfileController@postChangePassword', 'class' => 'form-horizontal')) }}
              <fieldset>

                <div class="form-group">
                  <label class="col-md-4 control-label" for="oldPassword">Old Password</label>
                  <div class="col-md-6">
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                      {{ Form::password('oldPassword', array('id' => 'oldPassword', 'class' => 'form-control'), 'required', 'autofocus') }}
                    </div>
                  </div>
                </div>

                <div class="form-group">
                  <label class="col-md-4 control-label" for="newPassword">New Password</label>
                  <div class="col-md-6">
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                      {{ Form::password('newPassword', array('id' => 'newPassword', 'class' => ' form-control'), 'required') }}
                    </div>
                  </div>
                </div>

                <div class="form-group">
                  <label class="col-md-4 control-label" for="confirmPassword">Confirm Password</label>
                  <div class="col-md-6">
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                      {{ Form::password('newPassword_confirmation', array('id' => 'confirmPassword', 'class' => ' form-control'), 'required') }}
                    </div>
                  </div>
                </div>

                <!-- Button -->
                <div class="form-group">
                  <label class="col-md-4 control-label" for="singlebutton"></label>
                  <div class="col-md-6">
                    {{ Form::submit('Change Password', array('class' => 'btn bg-olive btn-block')) }}
                  </div>
                </div>

              </fieldset>

            {{ Form::close() }}
          </p>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->

  <!-- account access-log modal modal -->
  <div class="modal fade" id="access-log-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title"><i class="fa fa-th-list"></i> View Account Access Log</h4>
        </div>
        <div class="modal-body">
          <span id="log-render"></span>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->

@stop

@section('javascript')
  <script type="text/javascript">

    // Bind a listener to the tabs which should load the required ajax for the
    // tab that is selected
    $("a#access-log").click(function() {

      console.log('aaai');

      // Populate the tab based on the url in locations
      $('span#log-render')
        .html('<br><p class="lead text-center"><i class="fa fa-cog fa fa-spin"></i> Loading the request...</p>')
        .load("{{ action('ProfileController@getAccessLog')}}");

    });

  </script>

@stop
