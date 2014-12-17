{{-- character notification --}}
<div class="row">
  <div class="col-md-12">
    <div class="box">
      <div class="box-header">
        <h3 class="box-title">Notifications ({{ count($notifications) }})</h3>
      </div><!-- /.box-header -->
      <div class="box-body no-padding">
        <table class="table table-hover table-condensed compact" id="datatable">
          <thead>
            <tr>
              <th style="width: 10px">#</th>
              <th>Date</th>
              <th>From</th>
              <th>Notification Type</th>
              <th>Sample</th>
              <th></th>
            </tr>
          </thead>
          <tbody>

            @foreach ($notifications as $note)

              <tr>
                <td>{{ $note->notificationID }}</td>
                <td data-order="{{ $note->sentDate }}">
                  <span data-toggle="tooltip" title="" data-original-title="{{ $note->sentDate }}">
                    {{ Carbon\Carbon::parse($note->sentDate)->diffForHumans() }}
                  </span>
                </td>
                <td>{{ $note->senderName }}</td>
                <td><b>{{ $note->description }}</b></td>
                <td><b>{{ str_limit($note->text, 80, $end = '...') }}</b></td>
                <td></td>
              </tr>

            @endforeach

          </tbody>
        </table>
      </div><!-- /.box-body -->
    </div>
  </div> <!-- ./col-md-12 -->
</div> <!-- ./row -->
