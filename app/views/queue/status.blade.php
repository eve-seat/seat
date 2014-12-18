@extends('layouts.masterLayout')

@section('html_title', 'Queue Status')

@section('page_content')

  <div class="row">
    <div class="col-md-12">
      <div class="small-box bg-purple">
        <div class="inner">
          <h3>{{ $redis_count }}</h3>
          <p>Jobs in the Redis Queue, with a Redis status of: {{ $redis_status }}</p>
        </div>
      </div>
    </div>
  </div>

  <div class="row">

    <div class="col-lg-3 col-xs-6">
      <!-- small box -->
      <div class="small-box bg-aqua">
        <div class="inner">
          <h3>{{ $db_queue_count }}</h3>
          <p>Queued Jobs</p>
        </div>
      </div>
    </div><!-- ./col -->

    <div class="col-lg-3 col-xs-6">
      <!-- small box -->
      <div class="small-box bg-yellow">
        <div class="inner">
          <h3>{{ $db_working_count }}</h3>
          <p> Working Jobs </p>
        </div>
      </div>
    </div><!-- ./col -->

    <div class="col-lg-2 col-xs-6">
      <!-- small box -->
      <div class="small-box bg-green">
        <div class="inner">
          <h3>{{ $db_done_count }}</h3>
          <p>Done Jobs</p>
        </div>
      </div>
    </div><!-- ./col -->

    <div class="col-lg-2 col-xs-6">
      <!-- small box -->
      <div class="small-box bg-red">
        <div class="inner">
          <h3>{{ $db_error_count }}</h3>
          <p>Error Jobs</p>
        </div>
      </div>
    </div><!-- ./col -->

    <div class="col-lg-2 col-xs-6">
      <!-- small box -->
      <div class="small-box bg-maroon">
        <div class="inner">
          <h3>

            @if (\Cache::has('eve_api_error_count'))

              {{ App\Services\Helpers\Helpers::format_number((\Cache::get('eve_api_error_count') / \Config::get('seat.error_limit')) * 100, 2) }}%
              <small>{{ \Cache::get('eve_api_error_count') }} / {{ \Config::get('seat.error_limit') }}</small>

            @else

              {{ (0 / \Config::get('seat.error_limit')) * 100 }}%
              <small>0 / {{ \Config::get('seat.error_limit') }}</small>

            @endif

          </h3>
          <p>EVE API Error Threshold</p>
        </div>
      </div>
    </div><!-- ./col -->
  </div> <!-- ./row -->

  <hr>

  <div class="row">
    <div class="col-md-4">
      <div class="box box-solid box-info">
        <div class="box-header">
          <h3 class="box-title">Queued Jobs</h3>
          <div class="box-tools pull-right">
            <button class="btn btn-info btn-sm"><i class="fa fa-minus" id="collapse-box"></i></button>
            <button class="btn btn-info btn-sm" data-widget="remove"><i class="fa fa-times"></i></button>
          </div>
        </div>
        <div class="box-body no-padding">

          @if (count($db_queue) > 0)

            <table class="table table-condensed">
              <thead>
                <tr>
                  <th>Owner</th>
                  <th>Scope</th>
                  <th>API</th>
                  <th>Created</th>
                  <th>Updated</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>

                @foreach ($db_queue as $queue)

                  <tr>
                    <td>{{ $queue->ownerID }}</td>
                    <td>{{ $queue->scope }}</td>
                    <td>{{ $queue->api }}</td>
                    <td>{{ $queue->output }}</td>
                    <td>{{ Carbon\Carbon::parse($queue->created_at)->diffForHumans() }}</td>
                    <td>{{ Carbon\Carbon::parse($queue->updated_at)->diffForHumans() }}</td>
                    <td><i class="fa fa-times" id="delete-queue" a-queue-id="{{ $queue->id }}" data-toggle="tooltip" title="" data-original-title="Delete Queued Job"></i></td>
                  </tr>

                @endforeach

              </tbody>
            </table>

          @else

            @if ($db_queue_count > 0)
              <h3><i class="fa fa-exclamation"></i> No Working Jobs, but there are jobs in the queue. Are the workers started?</h3>
            @else
              <h3><i class="fa fa-check"></i> No Working Jobs</h3>
            @endif

          @endif
        </div><!-- /.box-body -->
      </div> <!-- ./box -->
    </div><!-- ./ md-4 -->

    <div class="col-md-4">
      <!-- Danger box -->
      <div class="box box-solid box-warning">
        <div class="box-header">
          <h3 class="box-title">Current Working Jobs</h3>
          <div class="box-tools pull-right">
            <button class="btn btn-warning btn-sm"><i class="fa fa-minus" id="collapse-box"></i></button>
            <button class="btn btn-warning btn-sm" data-widget="remove"><i class="fa fa-times"></i></button>
          </div>
        </div>
        <div class="box-body no-padding">

          @if (count($db_working) > 0)

            <table class="table table-condensed">
              <thead>
                <tr>
                  <th>Owner</th>
                  <th>Scope</th>
                  <th>API</th>
                  <th>Status</th>
                  <th>Created</th>
                  <th>Updated</th>
                </tr>
              </thead>
              <tbody>

                @foreach ($db_working as $work)

                  <tr>
                    <td>{{ $work->ownerID }}</td>
                    <td>{{ $work->scope }}</td>
                    <td>{{ $work->api }}</td>
                    <td>{{ $work->output }}</td>
                    <td>{{ Carbon\Carbon::parse($work->created_at)->diffForHumans() }}</td>
                    <td>{{ Carbon\Carbon::parse($work->updated_at)->diffForHumans() }}</td>
                  </tr>

                @endforeach

              </tbody>
            </table>

          @else

            @if ($db_queue_count > 0)
              <h3><i class="fa fa-exclamation"></i> No Working Jobs, but there are jobs in the queue. Are the workers started?</h3>
            @else
              <h3><i class="fa fa-check"></i> No Working Jobs</h3>
            @endif

          @endif
        </div><!-- /.box-body -->
      </div><!-- /.box -->
    </div><!-- ./ md-4 -->

    <div class="col-md-4">
      <!-- Danger box -->

      @if (\Auth::isSuperUser())

        <div class="box box-solid box-danger">
          <div class="box-header">
            <h3 class="box-title">Last Error Messages</h3>
            <div class="box-tools pull-right">

              @if (count($db_errors) > 0)
                <button id="delete-all-errors" class="btn btn-danger btn-sm" data-widget="remove"><i class="fa fa-eraser"></i> Delete All</button> |
              @endif

              <button class="btn btn-danger btn-sm"><i class="fa fa-minus" id="collapse-box"></i></button>
              <button class="btn btn-danger btn-sm" data-widget="remove"><i class="fa fa-times"></i></button>
            </div>
          </div>
          <div class="box-body no-padding">

            @if (count($db_errors) > 0)

              <table class="table table-condensed">
                <thead>
                  <tr>
                    <th>Owner</th>
                    <th>Scope</th>
                    <th>API</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Updated</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>

                  @foreach ($db_errors as $error)

                    <tr>
                      <td>{{ $error->ownerID }}</td>
                      <td>{{ $error->scope }}</td>
                      <td>{{ $error->api }}</td>
                      <td>{{ str_limit($error->output, 100, '...') }}</td>
                      <td>{{ Carbon\Carbon::parse($error->created_at)->diffForHumans() }}</td>
                      <td>{{ Carbon\Carbon::parse($error->updated_at)->diffForHumans() }}</td>
                      <td>
                        <i class="fa fa-times" id="delete-error" a-error-id="{{ $error->id }}" data-toggle="tooltip" title="" data-original-title="Delete Error"></i>
                        <i class="fa fa-eye" id="view-full-error" a-error-id="{{ $error->id }}" data-toggle="tooltip" title="" data-original-title="View Full Error"></i>
                      </td>
                    </tr>

                  @endforeach
                </tbody>
              </table>

            @else
              <h3><i class="fa fa-check"></i> No Job Errors</h3>
            @endif

          </div><!-- /.box-body -->
        </div><!-- /.box -->
      @endif

      <!-- Job History box -->
      <div class="box box-solid box-success">
        <div class="box-header">
          <h3 class="box-title">Job History (Last {{ count($db_history) }})</h3>
          <div class="box-tools pull-right">
            <button class="btn btn-success btn-sm"><i class="fa fa-minus" id="collapse-box"></i></button>
            <button class="btn btn-success btn-sm" data-widget="remove"><i class="fa fa-times"></i></button>
          </div>
        </div>
        <div class="box-body no-padding">

          @if (!empty($db_history))

            <table class="table table-condensed">
              <thead>
                <tr>
                  <th>Owner</th>
                  <th>Scope</th>
                  <th>API</th>
                  <th>Status</th>
                  <th>Updated at</th>
                </tr>
              </thead>
              <tbody>

                @foreach ($db_history as $history)

                  <tr>
                    <td>{{ $history->ownerID }}</td>
                    <td>{{ $history->scope }}</td>
                    <td>{{ $history->api }}</td>
                    <td>{{ $history->status }}</td>
                    <td>{{ Carbon\Carbon::parse($history->updated_at)->diffForHumans() }}</td>
                  </tr>

                @endforeach

              </tbody>
            </table>

          @else
            <h3><i class="fa fa-check"></i> No Job Errors</h3>
          @endif

        </div><!-- /.box-body -->
      </div><!-- /.box -->
    </div> <!-- ./ md-4 -->
  </div>

@stop

@section('javascript')

  <script type="text/javascript">

    // Ajax Error Messages Deletion
    $("i#delete-error").click(function() {

      // Start rotating the icom indicating loading
      $(this).addClass('fa-spin');

      // Set the parent variable
      var parent = $(this).parent().parent();

      // Call the ajax and remove the row from the dom
      $.ajax({
        type: 'get',
        url: "{{ action('QueueController@getDeleteError') }}/" + $(this).attr('a-error-id'),
        success: function() {
          parent.remove();
        }
      });
    });

    // Ajax View Full Error Message
    $("i#view-full-error").click(function() {

      // Start rotating the icom indicating loading
      $(this).addClass('fa-spin');

      // Set the parent variable
      window.location = "{{ action('QueueController@getViewError') }}/" + $(this).attr('a-error-id')
    });

    // Ajax Delete All Error Messages
    $("button#delete-all-errors").click(function() {

      // Call the ajax and remove the row from the dom
      $.ajax({
        type: 'get',
        url: "{{ action('QueueController@getDeleteAllErrors') }}",
      });
    });

    // Ajax Error Messages Deletion
    $("i#delete-queue").click(function() {

      // Start rotating the icom indicating loading
      $(this).addClass('fa-spin');

      // Set the parent variable
      var parent = $(this).parent().parent();

      // Call the ajax and remove the row from the dom
      $.ajax({
        type: 'get',
        url: "{{ action('QueueController@getDeleteQueuedJob') }}/" + $(this).attr('a-queue-id'),
        success: function() {
          parent.remove();
        }
      });
    });

  </script>

@stop
