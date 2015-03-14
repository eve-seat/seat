@extends('layouts.masterLayout')

@section('html_title', 'Security Logs')

@section('page_content')

  <div class="row">
    <div class="col-md-12">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">
            <b>Security Logs</b>
            <button id="lookup" data-toggle="modal" data-target="#event-lookup" id="event-lookup" class="btn btn-primary btn-xs">Lookup User Event</button>
          </h3>
        </div>
        <div class="panel-body">
          <div>
            {{ $logs->links() }}
          </div>

          <table class="table table-condensed compact table-hover" id="datatable">
            <thead>
              <th>#</th>
              <th>Created At</th>
              <th>Triggered By</th>
              <th>Triggered For</th>
              <th>Path</th>
              <th>Message</th>
              <th>User IP</th>
              <th>Owned Keys</th>
              <th>Corp Afil</th>
            </thead>
            <tbody>

              @foreach ($logs as $log)

                <tr>
                  <td>{{ $log->id }}</td>
                  <td>{{ $log->created_at }}</td>
                  <td>{{ $log->triggered_by }}</td>
                  <td>{{ $log->triggered_for }}</td>
                  <td>{{ $log->path }}</td>
                  <td>{{ $log->message }}</td>
                  <td>{{ $log->user_ip }}</td>
                  <td>{{ $log->valid_keys }}</td>
                  <td>{{ $log->corporation_affiliations }}</td>
                </tr>

              @endforeach

            </tbody>
          </table>

        </div>
        <div class="panel-footer">
          <button id="lookup" data-toggle="modal" data-target="#event-lookup" id="event-lookup" class="btn btn-primary btn-xs">Lookup User Event</button>
        </div>

      </div>

    </div> <!-- col-md-12 -->
  </div> <!-- row -->

  <!-- event-lookup modal -->
  <div class="modal fade" id="event-lookup" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title"><i class="fa fa-th-list"></i> Event Lookup</h4>
        </div>
        <div class="modal-body">

          {{ Form::open(array('action' => 'LogController@postLookupSecurityEvent', 'class' => 'form-horizontal')) }}
            <fieldset>

              <div class="form-group">
                <label class="col-md-4 control-label" for="email">Event String</label>
                <div class="col-md-6">
                  <div class="input-group">
                    {{ Form::text('event', null, array('id' => 'event', 'class' => 'form-control', 'placeholder' => 'Event String'), 'required', 'autofocus') }}
                  </div>
                </div>
              </div>

              <!-- Button -->
              <div class="form-group">
                <label class="col-md-4 control-label" for="singlebutton"></label>
                <div class="col-md-4">
                  <button id="singlebutton" name="singlebutton" class="btn btn-primary"><i class="fa fa-plus"></i> Lookup Event</button>
                </div>
              </div>

            </fieldset>

          {{ Form::close()}}

        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->

@stop

