@extends('layouts.masterLayout')

@section('html_title', 'SeAT API Applications')

@section('page_content')

  <!-- build the global row-->
  <div class="row">

    <!-- split the row up into 2, so that we have the new application form on the right -->
    <div class="col-md-8">

      {{-- if we have no applications, alert on that, else loop over the existing ones --}}
      @if(count($applications) <= 0)
        <div class="row">
          <div class="col-md-12">
            <p class="text-center">Hey, it looks like you dont have any API Applications defined. How about creating one?</p>
          </div>
        </div>
      @else

        @foreach($applications as $application)

          <div class="row">
            <div class="col-md-12">
              <div class="panel panel-default">
                <div class="panel-heading">
                  <h3 class="panel-title">
                    <b>SeAT API Application #{{ $application->id }}</b>
                  </h3>
                </div>
                <div class="panel-body">

                  <div class="row">
                    <div class="col-md-6">

                      <dl>
                        <dt>Application Name</dt>
                        <dd>{{{ $application->application_name }}}</dd>
                        <dt>API Authentication Username</dt>
                        <dd>{{{ $application->application_login }}}</dd>
                        <dt>API Authentication Password</dt>
                        <dd>{{ $application->application_password }}</dd>
                      </dl>

                    </div>
                    <div class="col-md-6">

                      <dl>
                        <dt>Curl Usage Sample:</dt>
                        <dd>
                          <kbd>
                            $ curl -X POST --user "{{{ $application->application_login }}}:{{ $application->application_password }}" {{ secure_url('/api/v1/authenticate') }} --data "username=admin&password=adminpass"
                          </kbd>
                        </dd>
                      </dl>
                    </div>
                  </div>

                </div>

                <div class="panel-footer">
                  This 3rd Party API may be called from: <b>{{ $application->application_ip }}</b>
                  <span class="pull-right">

                    <a href="{{ action('SettingsController@getDeleteApiApplication', array('application_id' => $application->id)) }}" class="confirmlink">
                      <span class="label label-danger">Delete Application</span>
                    </a>
                    <span class="label label-info">View API Access Logs</span>

                  </span>
                </div>
              </div>

            </div> <!-- col-md-12 -->
          </div> <!-- row -->

        @endforeach

      @endif

    </div> <!-- global col-md-8 -->
    <div class="col-md-4">

      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">
            <b>New API Application</b>
          </h3>
        </div>
        <div class="panel-body">

          {{ Form::open(array('action' => 'SettingsController@postNewApiApplication', 'class' => 'form-horizontal')) }}
            <fieldset>

              <!-- Text input-->
              <div class="form-group">
                <label class="col-md-4 control-label" for="app_name">Application Name</label>
                <div class="col-md-6">
                  {{ Form::text('app_name', null, array('class' => 'form-control input-md')) }}
                  <span class="help-block">A unique name your 3rd party application will be recognized as.</span>
                </div>
              </div>

              <!-- Text input-->
              <div class="form-group">
                <label class="col-md-4 control-label" for="app_src">Source Access IP</label>
                <div class="col-md-6">
                  {{ Form::text('app_src', null, array('class' => 'form-control input-md')) }}
                  <span class="help-block">The IP Address SeAT will see API requests originating from.</span>
                </div>
              </div>

              <!-- Button -->
              <div class="form-group">
                <label class="col-md-4 control-label" for="singlebutton"></label>
                <div class="col-md-4">
                  <button id="singlebutton" name="singlebutton" class="btn btn-primary"><i class="fa fa-plus"></i> Add New Application</button>
                </div>
              </div>

            </fieldset>

          {{ Form::close()}}

        </div>
        <div class="panel-footer">
          Authentication Credentials will be auto-generated.
        </div>
      </div>

    </div>

  </div> <!-- global row -->

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
