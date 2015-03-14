@extends('layouts.masterLayout')

@section('html_title', 'Security Logs')

@section('page_content')

  <div class="row">
    <div class="col-md-12">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">
            <b>Security Logs</b>
          </h3>
        </div>
        <div class="panel-body">
          <div class="">{{ $logs->links() }}</div>

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

      </div>

    </div> <!-- col-md-12 -->
  </div> <!-- row -->

@stop
