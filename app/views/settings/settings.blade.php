@extends('layouts.masterLayout')

@section('html_title', 'SeAT Settings')

@section('page_content')

  <div class="row">
    <div class="col-md-8">
      <div class="box" id="result">
        <div class="box-header">
          <h3 class="box-title">SeAT Settings</h3>
        </div>

        <div class="box-body">

          {{ Form::open(array('action' => 'SettingsController@postUpdateSetting', 'class' => 'form-horizontal')) }}
            <fieldset>

              <legend>Global Settings</legend>

              <!-- Application Name-->
              <div class="form-group">
                <label class="col-md-4 control-label" for="textinput">Application Name</label>
                <div class="col-md-4">
                  {{ Form::text('app_name', $app_name, array('id' => 'app_name', 'class' => 'form-control'), 'required', 'autofocus') }}
                  <span class="help-block">The Name of your SeAT Instance.</span>
                </div>
              </div>

              <!-- Prepended text-->
              <div class="form-group">
                <label class="col-md-4 control-label" for="prependedtext">Registration Enabled</label>
                <div class="col-md-6">
                  <div class="input-group">
                    {{ Form::select('registration_enabled', array('true' => 'Yes', 'false' => 'No'), $registration_enabled, array('class' => 'form-control')) }}
                    <span class="help-block">Are external parties allowed to register to your SeAT instance.</span>
                  </div>
                </div>
              </div>

              <!-- Prepended text-->
              <div class="form-group">
                <label class="col-md-4 control-label" for="prependedtext">Required API Mask</label>
                <div class="col-md-6">
                  <div class="input-group">
                    {{ Form::text('required_mask', $required_mask, array('id' => 'required_mask', 'class' => 'form-control'), 'required') }}
                    <span class="help-block">What is the minimum required API Key access mask.</span>
                  </div>
                </div>
              </div>

              <!-- Prepended text-->
              <div class="form-group">
                <label class="col-md-4 control-label" for="prependedtext">Color Scheme</label>
                <div class="col-md-6">
                  <div class="input-group">
                    {{ Form::select('color_scheme', array('black' => 'black', 'blue' => 'blue'), $color_scheme, array('class' => 'form-control')) }}
                  </div>
                </div>
              </div>

              <!-- Prepended text-->
              <div class="form-group">
                <label class="col-md-4 control-label" for="prependedtext">Number Format</label>
                <div class="col-md-6">
                  <div class="form-inline input-group">
                    100
                    {{ Form::select('thousand_seperator', array('.' => '.', ',' => ',', ' ' => '(space)'), $thousand_seperator, array('class' => 'form-inline form-control')) }}
                    000
                    {{ Form::select('decimal_seperator', array('.' => '.', ',' => ','), $decimal_seperator, array('class' => 'form-control')) }}
                    00
                  </div>
                  <span class="help-block">Set the thousand and decimal character, e.g: 100,000.00</span>
                </div>
              </div>

              <legend>Seat Queue Settings</legend>

              <p>
                <b>Note:</b> These parameters affect the SeAT Job Queuing system. Disabling a section here will cease all update work for the affected section.
              </p>

              <!-- Prepended text-->
              <div class="form-group">
                <label class="col-md-4 control-label" for="prependedtext">Character Updater</label>
                <div class="col-md-6">
                  <div class="input-group">
                    {{ Form::select('seatscheduled_character', array('true' => 'Yes', 'false' => 'No'), $seatscheduled_character, array('class' => 'form-control')) }}
                    <span class="help-block">Updates all character related information.</span>
                  </div>
                </div>
              </div>

              <!-- Prepended text-->
              <div class="form-group">
                <label class="col-md-4 control-label" for="prependedtext">Corporation Updater</label>
                <div class="col-md-6">
                  <div class="input-group">
                    {{ Form::select('seatscheduled_corporation', array('true' => 'Yes', 'false' => 'No'), $seatscheduled_corporation, array('class' => 'form-control')) }}
                    <span class="help-block">Updates all Corporation related information, except for Assets and Wallets.</span>
                  </div>
                </div>
              </div>

              <!-- Prepended text-->
              <div class="form-group">
                <label class="col-md-4 control-label" for="prependedtext">Corporation Assets Updater</label>
                <div class="col-md-6">
                  <div class="input-group">
                    {{ Form::select('seatscheduled_corporation_assets', array('true' => 'Yes', 'false' => 'No'), $seatscheduled_corporation_assets, array('class' => 'form-control')) }}
                    <span class="help-block">Updates all Corporation Assets related information.</span>
                  </div>
                </div>
              </div>

              <!-- Prepended text-->
              <div class="form-group">
                <label class="col-md-4 control-label" for="prependedtext">Corporation Wallets Updater</label>
                <div class="col-md-6">
                  <div class="input-group">
                    {{ Form::select('seatscheduled_corporation_wallets', array('true' => 'Yes', 'false' => 'No'), $seatscheduled_corporation_wallets, array('class' => 'form-control')) }}
                    <span class="help-block">Updates all Corporation Wallets related information.</span>
                  </div>
                </div>
              </div>

              <!-- Prepended text-->
              <div class="form-group">
                <label class="col-md-4 control-label" for="prependedtext">Eve Updater</label>
                <div class="col-md-6">
                  <div class="input-group">
                    {{ Form::select('seatscheduled_eve', array('true' => 'Yes', 'false' => 'No'), $seatscheduled_eve, array('class' => 'form-control')) }}
                    <span class="help-block">Updates all Eve related information.</span>
                  </div>
                </div>
              </div>

              <!-- Prepended text-->
              <div class="form-group">
                <label class="col-md-4 control-label" for="prependedtext">Map Updater</label>
                <div class="col-md-6">
                  <div class="input-group">
                    {{ Form::select('seatscheduled_map', array('true' => 'Yes', 'false' => 'No'), $seatscheduled_map, array('class' => 'form-control')) }}
                    <span class="help-block">Updates all Map related information.</span>
                  </div>
                </div>
              </div>

              <!-- Prepended text-->
              <div class="form-group">
                <label class="col-md-4 control-label" for="prependedtext">Server Updater</label>
                <div class="col-md-6">
                  <div class="input-group">
                    {{ Form::select('seatscheduled_server', array('true' => 'Yes', 'false' => 'No'), $seatscheduled_server, array('class' => 'form-control')) }}
                    <span class="help-block">Updates all Server related information.</span>
                  </div>
                </div>
              </div>

              <!-- Prepended text-->
              <div class="form-group">
                <label class="col-md-4 control-label" for="prependedtext">Notification Updater</label>
                <div class="col-md-6">
                  <div class="input-group">
                    {{ Form::select('seatscheduled_notifications', array('true' => 'Yes', 'false' => 'No'), $seatscheduled_notifications, array('class' => 'form-control')) }}
                    <span class="help-block">Processes all Notifications related information.</span>
                  </div>
                </div>
              </div>

              <!-- Prepended text-->
              <div class="form-group">
                <label class="col-md-4 control-label" for="prependedtext">Queue Cleaner</label>
                <div class="col-md-6">
                  <div class="input-group">
                    {{ Form::select('seatscheduled_queue_cleanup', array('true' => 'Yes', 'false' => 'No'), $seatscheduled_queue_cleanup, array('class' => 'form-control')) }}
                    <span class="help-block">Cleans the Job Queue of long running jobs.</span>
                  </div>
                </div>
              </div>

              <!-- Button -->
              <div class="form-group">
                <label class="col-md-4 control-label" for="singlebutton"></label>
                <div class="col-md-6">
                  <button id="check-key" name="singlebutton" class="btn btn-success">Update Settings</button>
                </div>
              </div>

            </fieldset>
          {{ Form::close() }}

        </div><!-- /.box-body -->
      </div><!-- /.box -->
    </div><!-- /.col-md-8 -->

    <div class="col-md-4">
      <div class="box">
        <div class="box-header">
          <h3 class="box-title">Module Manager</h3>
        </div>

        <div class="box-body table-responsive">
          Not yet, but sooooooooon!
        </div><!-- ./box-body -->

      </div><!-- ./box -->
    </div><!-- ./col-md-4 -->
  </div><!-- ./row -->

@stop
