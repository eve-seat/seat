@extends('layouts.masterLayout')

@section('html_title', 'Key Details')

@section('page_content')

  <div class="row">
    <div class="col-lg-3 col-xs-6">
      <!-- small box -->
      <div class="small-box bg-blue">
        <div class="inner">
          <h3>{{ $key_information->keyID }}</h3>
          <p>
            Key ID
            <a class="btn btn-primary btn-xs pull-right" data-toggle="modal" data-target="#vcode-modal"><i class="fa fa-circle-o"></i> Reveal vCode</a>
          </p>
        </div>
      </div>
    </div><!-- ./col -->

    <div class="col-lg-3 col-xs-6">
      <!-- small box -->
      <div class="small-box bg-green">
        <div class="inner">
          <h3>
            @if (strlen($key_information->type) > 0)
              {{ $key_information->type }}
            @else
              Unknown
            @endif
          </h3>
          <p>Key Type</p>
        </div>
      </div>
    </div><!-- ./col -->

    <div class="col-lg-3 col-xs-6">
      <!-- small box -->
      <div class="small-box bg-maroon">
        <div class="inner">
          <h3>{{ $key_information->accessMask }}</h3>
          <p>Access Mask</p>
        </div>
      </div>
    </div><!-- ./col -->

    <div class="col-lg-3 col-xs-6">
      <!-- small box -->
      @if ($key_information->isOk == 1)
        <div class="small-box bg-green">
      @else
        <div class="small-box bg-red">
      @endif

          <div class="inner">
            <h3>{{ $key_information->isOk }}</h3>
            <p>
              @if ($key_information->isOk == 1)
                Key is OK
              @else
                Key is NOT ok <a href="{{ action('ApiKeyController@getEnableKey', array('keyID' => $key_information->keyID )) }}" class="btn btn-success btn-xs pull-right" data-container="body" data-toggle="popover" data-placement="left" data-content="{{ $key_information->lastError }}" data-trigger="hover"><i class="fa fa-refresh"></i> Re-enable Key</a>
              @endif
            </p>
          </div>
        </div>
      </div><!-- ./col -->
    </div>

  <hr>

  <div class="row">
    <div class="col-md-4">
      <!-- Danger box -->
      <div class="box box-solid box-primary">
        <div class="box-header">
          <h3 class="box-title">Characters On Key</h3>
          <div class="box-tools pull-right">
            <button class="btn btn-primary btn-sm" data-widget="collapse"><i class="fa fa-minus"></i></button>
            <button class="btn btn-primary btn-sm" data-widget="remove"><i class="fa fa-times"></i></button>
          </div>
        </div>
        <div class="box-body">
          @if (count($key_characters) > 0)

            @foreach ($key_characters as $character)

              <div class="row">
                <div class="col-md-4">
                  <a href="{{ action('CharacterController@getView', array('characterID' => $character->characterID )) }}">
                    <img src="//image.eveonline.com/Character/{{ $character->characterID }}_64.jpg" class="img-circle pull-right">
                  </a>
                </div>
                <div class="col-md-8">
                  <p class="lead">{{ $character->characterName }}</p>
                  <p>{{ $character->corporationName }}</p>
                </div>
              </div>

            @endforeach

          @else
            No known characters on this key
          @endif
        </div><!-- /.box-body -->
      </div><!-- /.box -->

      {{-- access mask details --}}
      <div class="box box-solid box-info">
        <div class="box-header">
          <h3 class="box-title">Key Access Mask Breakdown</h3>
          <div class="box-tools pull-right">
            <button class="btn btn-info btn-sm" data-widget="collapse"><i class="fa fa-minus"></i></button>
            <button class="btn btn-info btn-sm" data-widget="remove"><i class="fa fa-times"></i></button>
          </div>
        </div>
        <div class="box-body no-padding">
          <table class="table table-condensed table-hover">
            <thead>
              <tr>
                <th>Endpoint</th>
                <th>Access</th>
              </tr>
            </thead>
            <tbody>

              @foreach (App\Services\Helpers\Helpers::processAccessMask($key_information->accessMask, $key_information->type) as $endpoint => $access)

                <tr>
                  <td>{{ $endpoint }}</td>
                  <td>
                    @if ($access == 'true')
                    <span class="text-green">{{ $access }}</span>
                    @else
                    <span class="text-red">{{ $access }}</span>
                    @endif
                  </td>
                </tr>

              @endforeach

            </tbody>
          </table>
        </div><!-- /.box-body -->
      </div><!-- ./box -->
    </div><!-- /.col -->

    <div class="col-md-4">

      @if(Auth::isSuperUser())

        <div class="box box-solid">
          <div class="box-header">
            <h3 class="box-title">Key Ownership</h3>
            <div class="box-tools pull-right">
              <button class="btn btn-default btn-sm" data-widget="collapse"><i class="fa fa-minus"></i></button>
              <button class="btn btn-default btn-sm" data-widget="remove"><i class="fa fa-times"></i></button>
            </div>
          </div>
          <div class="box-body">
            <ul class="list-unstyled">

              @foreach($key_owner as $owner)

                <li><a href="{{ action('UserController@getDetail', array('userID' => $owner->id)) }}"><i class="fa fa-user"></i> {{ $owner->username }}</a> ({{ $owner->email }})</li>

              @endforeach

            </ul>

          </div><!-- /.box-body -->
          <div class="box-footer">
            <a id="transfer" data-toggle="modal" data-target="#transfer-modal" class="btn btn-primary btn-xs">Transfer Key Ownership</a>
          </div>
        </div>
      @endif

      <!-- Success box -->
      <div class="box box-solid box-success">
        <div class="box-header">
          <h3 class="box-title">Recent Update Jobs</h3>
          <div class="box-tools pull-right">
            <button class="btn btn-success btn-sm" data-widget="collapse"><i class="fa fa-minus"></i></button>
            <button class="btn btn-success btn-sm" data-widget="remove"><i class="fa fa-times"></i></button>
          </div>
        </div>

          @if (count($recent_jobs) > 0)

            <div class="box-body no-padding">
              <table class="table table-condensed table-hover">
                <thead>
                  <tr>
                    <th>Scheduled</th>
                    <th>API</th>
                    <th>Scope</th>
                    <th>Status</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>

                  @foreach ($recent_jobs as $job)

                    <tr>
                      <td>
                        <span data-toggle="tooltip" title="" data-original-title="{{ $job->created_at }}">
                          {{ Carbon\Carbon::parse($job->created_at)->diffForHumans() }}
                        </span>
                      </td>
                      <td>{{ $job->api }}</td>
                      <td>{{ $job->scope }}</td>
                      <td>{{ $job->status }}</td>
                      <td>
                        @if (strlen($job->output) > 0)
                          <i class="fa fa-bullhorn pull-right" data-container="body" data-toggle="popover" data-placement="top" data-content="{{ $job->output }}" data-trigger="hover"></i>
                        @endif
                      </td>
                    </tr>

                  @endforeach

                </tbody>
              </table>

          @else

            <div class="box-body">
              No recent jobs have been run for this key
          @endif

          </div><!-- /.box-body -->
        </div><!-- /.box -->
        <div id="job-result">
          {{-- just provide the update now for character keys --}}
          @if ($key_information->keyID <> 'Corporation')
            <button class="btn btn-primary btn-block" id="new-job">Update Key Now</button>
          @endif
        </div>
      </div><!-- /.col -->

      <div class="col-md-4">
        <!-- Warning box -->
        <div class="box box-solid box-warning">
          <div class="box-header">
            <h3 class="box-title">Banned Calls</h3>
            <div class="box-tools pull-right">
              <button class="btn btn-warning btn-sm" data-widget="collapse"><i class="fa fa-minus"></i></button>
              <button class="btn btn-warning btn-sm" data-widget="remove"><i class="fa fa-times"></i></button>
            </div>
          </div>

            @if (count($key_bans) > 0)

              <div class="box-body no-padding">
                <table class="table table-condensed table-hover">
                  <thead>
                    <tr>
                      <th>Scheduled</th>
                      <th>Access Mask</th>
                      <th>API</th>
                      <th>Scope</th>
                      <th></th>
                    </tr>
                  </thead>
                  <tbody>

                    @foreach ($key_bans as $ban)

                      <tr>
                        <td>
                          <span data-toggle="tooltip" title="" data-original-title="{{ $ban->created_at }}">
                            {{ Carbon\Carbon::parse($ban->created_at)->diffForHumans() }}
                          </span>
                        </td>
                        <td>{{ $ban->accessMask }}</td>
                        <td>{{ $ban->api }}</td>
                        <td>{{ $ban->scope }}</td>
                        <td>
                          @if (strlen($ban->reason) > 0)
                            <i class="fa fa-bullhorn pull-right" data-container="body" data-toggle="popover" data-placement="top" data-content="{{ $ban->reason }}" data-trigger="hover"></i>
                          @endif
                        </td>
                        <td><i class="fa fa-times" id="remove-ban" a-ban-id="{{ $ban->id }}" data-toggle="tooltip" title="" data-original-title="Remove Ban"></i></td>
                      </tr>

                    @endforeach

                  </tbody>
                </table>

              @else

            <div class="box-body">
              No banned calls for this key
          @endif
        </div><!-- /.box-body -->
      </div><!-- /.box -->
    </div><!-- /.col -->
  </div>

  <!-- vCode reveal modal -->
  <div class="modal fade" id="vcode-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title"><i class="fa fa-eye"></i> Full vCode</h4>
        </div>
        <div class="modal-body">
          <p class="text-center"><b>{{ $key_information->vCode }}</b></p>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->

  <!-- transfer ownership modal -->
  <div class="modal fade" id="transfer-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title"> Transfer Key Ownership to Another SeAT Account</h4>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12">

              {{ Form::open(array('class' => 'form-horizontal', 'action' => 'ApiKeyController@postTransferOwnership')) }}

                <fieldset>

                  <!-- Prepended text-->
                  <div class="form-group">
                    <label class="col-md-4 control-label" for="searchinput"></label>
                    <div class="input-group">
                      {{ Form::text('accountid', null, array('id' => 'searchinput', 'class' => 'form-control'), 'required', 'autofocus') }}
                    </div>
                  </div>

                  <input id="keyID" name="keyID" type="hidden" value="{{ $key_information->keyID }}">

                  <div class="form-group">
                    <label class="col-md-4 control-label"></label>
                    <div class="input-group">
                      {{ Form::submit('Transfer', array('class' => 'btn bg-olive')) }}
                    </div>
                  </div>

                </fieldset>

              {{ Form::close() }}

            </div>
          </div>

        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->

@stop

@section('javascript')

  <script type="text/javascript">
    $("button#new-job").click(function() {

      $(this).addClass('disabled');
      $.ajax({
        type: "get",
        url: "{{ action('ApiKeyController@getUpdateJob', array('keyID' => $key_information->keyID)) }}",
        success: function(data) {
          if (data.state == 'error') {
            $("div#job-result").html('An error occured when trying to schedule a update job for this key. The application logs may be able to tell you why.');
          }
          if (data.state == 'existing') {
            $("div#job-result").html('An existing queued update job for this keyID is present with jobID ' + data.jobID);
          }
          if (data.state == 'new') {
            $("div#job-result").html('A new update job was scheduled with jobID ' + data.jobID);
          }
        },
        complete: function(data) {
          console.log(data)
        }
      });
    });

    // Ajax Ban Removal
    $("i#remove-ban").click(function() {

      // Start rotating the icom indicating loading
      $(this).addClass('fa-spin');

      // Set the parent variable
      var parent = $(this).parent().parent();

      // Call the ajax and remove the row from the dom
      $.ajax({
        type: 'get',
        url: "{{ action('ApiKeyController@getRemoveBan') }}/" + $(this).attr('a-ban-id'),
        success: function() {
          parent.remove();
        }
      });
    });

    // Search for avaialble accounts
    $('#searchinput').select2({
      multiple: false,
      width: "250",
      placeholder: "Select the destination account username",
      minimumInputLength: 1,
      ajax: {
        url: "{{ action('HelperController@getAccounts') }}",
        dataType: 'json',
        data: function (term, page) {
          return {
            q: term
          };
        },
        results: function (data, page) {
          return { results: data };
        }
      }
    });

  </script>

@stop
