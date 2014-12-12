@extends('layouts.masterLayout')

@section('html_title', 'Notifications')

@section('page_content')

  <div class="row">
    <div class="col-md-12">
      <div class="box">

        <div class="box-header">
          <h3 class="box-title">Notifications ({{ $unread }} unread)</h3>
        </div>

        <div class="box-body">
          <table class="table table-condensed table-hover" id="datatable">
            <thead>
              <tr>
                <th>Notification Title</th>
                <th>Notification Type</th>
                <th>Notification Status</th>
                <th>Created</th>
                <th></th>
                <th></th>
              </tr>
            </thead>
            <tbody>

              @foreach($notifications as $notification)

                <tr>
                  <td>{{ $notification->title }}</td>
                  <td>{{ $notification->type }}</td>
                  <td>{{ ($notification->read ? 'Read' : 'Unread') }}</td>
                  <td>{{ $notification->created_at->format('h:m:s / d-m-Y') }}</td>
                  <td>
                    <a href="{{ action('NotificationController@getDetail', array('notificationID' => $notification->id)) }}" class="btn btn-info btn-xs"><i class="fa fa-desktop"></i> View Notification</a>
                  </td>
                  <td>
                    @if($notification->read)
                    <a href="{{ action('NotificationController@getMarkUnread', array('notificationID' => $notification->id)) }}" class="btn btn-danger btn-xs"><i class="fa fa-close"></i> Mark Unread</a>
                    @else
                    <a href="{{ action('NotificationController@getMarkRead', array('notificationID' => $notification->id)) }}" class="btn btn-success btn-xs"><i class="fa fa-check"></i> Mark Read</a>
                    @endif
                  </td>
                </tr>

              @endforeach

            </tbody>
          </table>
        </div><!-- /.box-body -->
      </div><!-- /.box -->
    </div>
  </div>

@stop

@section('javascript')
  <script type="text/javascript">
    $(document).on("click", ".delete-user", function(e) {

      // Save the links
      var delete_user = $(this).attr("a-delete-user");

      // Provide the user a option to keep the existing data, or delete everything we know about the key
      bootbox.dialog({
        message: "Please confirm whether you want to delete the user?",
        title: "Delete user " + $(this).attr("a-user-name"),
        buttons: {
          success: {
            label: "No Thanks",
            className: "btn-default"
          },
          danger: {
            label: "Delete User",
            className: "btn-danger",
            callback: function() {
              window.location = delete_user;
            }
          }
        }
      });
    });
  </script>

@stop
