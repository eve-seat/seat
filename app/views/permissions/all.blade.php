@extends('layouts.masterLayout')

@section('html_title', 'Permissions Management')

@section('page_content')

  {{-- open a empty form to get a crsf token --}}
  {{ Form::open(array()) }} {{ Form::close() }}

  <div id="notification"></div>

  <div class="row">
    <div class="col-md-2">
      <ul class="nav nav-pills nav-stacked" id="available-corporations">
        <li class="header">Available Corporations</li>

          @foreach ($corporations as $corp)
            <li><a href="#{{ $corp->corporationID }}" id="corporation" data-toggle="tab" a-corporation-id="{{ $corp->corporationID }}">{{ $corp->corporationName }}</a></li>
          @endforeach

      </ul>
    </div>

    <div class="col-md-10">

      <div id="load-result">
        <p class="lead">Select a Corporation</p>
      </div>

    </div> <!-- ./tab-content -->
  </div>

@stop

@section('javascript')

  <script type="text/javascript">

    // variable to hold request
    var request;

    $('a#corporation').click(function() {

      $('#load-result')
        .html('<br><i class="fa fa-cog fa fa-spin"></i> Loading Permissions...')
        .load("{{ action('PermissionsController@getCorporation') }}" + "/" + $(this).attr('a-corporation-id'));

      $("table#datatable").dataTable({ paging:false });
    });

    // Bind events to the HTML that we will be getting from the AJAX response
    $("div#load-result").delegate('input#permission', 'click', function() {

      // abort any pending request
      if (request) {
          request.abort();
      }

      var groupname = $(this).attr('a-group-name');
      var user_id = $(this).attr('a-user-id');

      request = $.ajax({
        url: "{{ action('PermissionsController@postSetPermission') }}",
        type: "post",
        data: { group: groupname, user: user_id }
      });

      // callback handler that will be called on success
      request.done(function (response, textStatus, jqXHR) {
        $("div#notification").show().html("<div class='alert alert-success alert-dismissable'> <i class='fa fa-check'></i> <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> <b>Success!</b> The permissions change has been applied </div>"
          ).fadeOut(2000);
      });

      // callback handler that will be called on failure
      request.fail(function (jqXHR, textStatus, errorThrown) {
        // log the error to the console
        console.error(
          "The following error occured: " + textStatus, errorThrown
        );
      });
    });

  </script>

@stop
