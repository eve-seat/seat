@extends('layouts.masterLayout')

@section('html_title', 'API Debugger')

@section('page_content')

  <div class="row">

    <div class="col-md-5">

      <div class="box">
        <div class="box-header">
          <h3 class="box-title">API Details</h3>
        </div>

        <div class="box-body table-responsive">

          {{ Form::open(array('class' => 'form-horizontal', 'id' => 'api-form')) }}

            <fieldset>

              <!-- Select Basic -->
              <div class="form-group">
                <label class="col-md-4 control-label" for="api">API</label>
                <div class="col-md-4">
                  <select id="api" name="api" class="form-control">
                    <option value="Account">Account</option>
                    <option value="char">Character</option>
                    <option value="corp">Corporation</option>
                    <option value="Eve">Eve</option>
                    <option value="Map">Map</option>
                    <option value="Server">Server</option>
                  </select>
                </div>
              </div>

              <!-- Select Basic -->
              <div class="form-group">
                <label class="col-md-4 control-label" for="call">Call</label>
                <div class="col-md-4">
                  <select id="call" name="call" class="form-control">

                    @foreach ($call_list as $call => $details)

                      <option value="{{ $call }}">{{ $call }}</option>

                    @endforeach

                  </select>
                </div>
              </div>

              <!-- Text input-->
              <div class="form-group">
                <label class="col-md-4 control-label" for="keyid">KeyID</label>
                <div class="col-md-4">
                  <input id="keyID" name="keyID" type="text" placeholder="Key ID" class="form-control input-md">
                </div>
              </div>

              <!-- Text input-->
              <div class="form-group">
                <label class="col-md-4 control-label" for="vcode">vCode</label>
                <div class="col-md-4">
                  <input id="vCode" name="vCode" type="text" placeholder="vCode" class="form-control input-md">
                </div>
              </div>

              <!-- Text input-->
              <div class="form-group">
                <label class="col-md-4 control-label" for="characterid">CharacterID</label>
                <div class="col-md-4">
                  <input id="characterid" name="characterid" type="text" placeholder="CharacterID" class="form-control input-md">
                </div>
              </div>

              <!-- Text input-->
              <div class="form-group">
                <label class="col-md-4 control-label" for="optional1">Optional Arg 1</label>
                <div class="col-md-4">
                  <input id="optional1" name="optional1" type="text" placeholder="Arg Name" class="form-control input-md">
                </div>
              </div>

              <!-- Text input-->
              <div class="form-group">
                <label class="col-md-4 control-label" for="optional1value">Optional Value 1</label>
                <div class="col-md-4">
                  <input id="optional1value" name="optional1value" type="text" placeholder="Arg Value" class="form-control input-md">
                </div>
              </div>

              <!-- Text input-->
              <div class="form-group">
                <label class="col-md-4 control-label" for="optional2">Optional Arg 2</label>
                <div class="col-md-4">
                  <input id="optional2" name="optional2" type="text" placeholder="Arg Name" class="form-control input-md">
                </div>
              </div>

              <!-- Text input-->
              <div class="form-group">
                <label class="col-md-4 control-label" for="optional2value">Optional Value 2</label>
                <div class="col-md-4">
                  <input id="optional2value" name="optional2value" type="text" placeholder="Arg Value" class="form-control input-md">
                </div>
              </div>

              <!-- Button -->
              <div class="form-group">
                <label class="col-md-4 control-label" for="singlebutton"></label>
                <div class="col-md-4">
                  <button id="singlebutton" name="singlebutton" class="btn btn-sm btn-default btn-block"><i class="fa fa-check-square"></i> Run Test</button>
                </div>
              </div>

            </fieldset>

          {{ Form::close() }}

        </div><!-- /.box-body -->
      </div><!-- /.box -->

    </div> <!-- ./md-6 -->
    <div class="col-md-7">

      <!-- results box -->
      <div class="box" id="result-box" style="display: none;">
        <div class="box-header">
          <h3 class="box-title">Results</h3>
        </div>

        <div class="box-body">
          <div id="result">
          </div>
        </div><!-- /.box-body -->
      </div><!-- /.box -->
    </div><!-- ./md-6 -->

  </div> <!-- ./row -->

@stop

@section('javascript')

  <script type="text/javascript">

    $("select#api").select2();
    $("select#call").select2();

    // variable to hold request
    var request;
    // bind to the submit event of our form
    $("#api-form").submit(function(event){

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
      $("div#result").html("<i class='fa fa-cog fa-spin'></i> Processing the API Call...");
      $("div#result-box").fadeIn("slow");

      // fire off the request to /form.php
      request = $.ajax({
          url: "{{ action('DebugController@postQuery') }}",
          type: "post",
          data: serializedData
      });

      // callback handler that will be called on success
      request.done(function (response, textStatus, jqXHR){
          $("div#result").html(response);
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
