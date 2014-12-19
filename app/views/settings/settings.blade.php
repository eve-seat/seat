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

          {{ Form::open(array('class' => 'form-horizontal', 'id' => 'settings-form')) }}
            <fieldset>

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

@section('javascript')

  <script type="text/javascript">

    // variable to hold request
    var request;

    // bind to the submit event of our form
    $("#settings-form").submit(function(event){

      // abort any pending request
      if (request) {
        request.abort();
      }

      // setup some local variables
      var $form = $(this);

      // let's select and cache all the fields
      var $inputs = $form.find("input, select, button, textarea");

      // serialize the data in the form
      var serializedData = $form.serialize();

      // let's disable the inputs for the duration of the ajax request
      // Note: we disable elements AFTER the form data has been serialized.
      // Disabled form elements will not be serialized.
      $inputs.prop("disabled", true);

      // Show the results box and a loader
      $("div#result").html("<i class='fa fa-cog fa-spin'></i> Loading...");
      $("div#result-box").fadeIn("slow");

      // fire off the request to /form.php
      request = $.ajax({
          url: "{{ action('SettingsController@postUpdateSetting') }}",
          type: "post",
          data: serializedData
      });

      // callback handler that will be called on success
      request.done(function (response, textStatus, jqXHR){
          //$("div#result").html(response);
          location.reload();
      });

      // callback handler that will be called on failure
      request.fail(function (jqXHR, textStatus, errorThrown){
          // log the error to the console
          console.error(
              "The following error occured: " + textStatus, errorThrown
          );
      });

      // callback handler that will be called regardless
      // if the request failed or succeeded
      request.always(function () {
          // reenable the inputs
          $inputs.prop("disabled", false);
      });

      // prevent default posting of form
      event.preventDefault();

    });
  </script>

@stop
