{{-- character calendar events --}}
<div class="row">
  <div class="col-md-12">
    <div class="box">
      <div class="box-header">
        <h3 class="box-title">Calendar Events ({{ count($calendar_events) }})</h3>
      </div><!-- /.box-header -->
      <div class="box-body no-padding">
        <table class="table table-hover table-condensed compact" id="datatable">
          <thead>
            <tr>
              <th>Owner Name</th>
              <th>Event Date</th>
              <th>Event Title</th>
              <th>Duration</th>
              <th>Response</th>
              <th>Event Text</th>
            </tr>
          </thead>
          <tbody>

            @foreach ($calendar_events as $event)

              <tr>
                <td>{{ $event->ownerName }}</td>
                <td>{{ $event->eventDate }}</td>
                <td>{{ $event->eventTitle }}</td>
                <td>{{ $event->duration }}</td>
                <td>{{ $event->response }}</td>
                <td>{{ $event->eventText }}</td>
              </tr>

            @endforeach

          </tbody>
        </table>
      </div><!-- /.box-body -->
    </div>
  </div> <!-- ./col-md-12 -->
</div> <!-- ./row -->
