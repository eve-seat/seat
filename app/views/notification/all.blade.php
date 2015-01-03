@extends('layouts.masterLayout')

@section('html_title', 'Notifications')

@section('page_content')

  <div class="row">
    <div class="col-md-12">
      <div class="box">

        <div class="box-header">
          <h3 class="box-title">
            Notification(s) ({{ $unread }} unread)
            <a href="{{ action('NotificationController@getMarkAllRead') }}" class="btn btn-sm btn-default confirmlink">Mark All As Read</a>
          </h3>
          <div class="box-tools">
            <ul class="pagination pagination-sm no-margin pull-right">
              {{ $notifications->links() }}
            </ul>
          </div>
        </div>

        <div class="box-body">
          <table class="table table-condensed table-hover compact" id="datatable">
            <thead>
              <tr>
                <th>Date</th>
                <th>Notification Type</th>
                <th>Notification Title</th>
                <th>Notification Status</th>
                <th></th>
                <th></th>
              </tr>
            </thead>
            <tbody>

              @foreach($notifications as $notification)

                <tr {{ ($notification->read ? : 'class="active"') }}>
                  <td>
                    <span data-toggle="tooltip" title="" data-original-title="{{ $notification->created_at }}">
                      {{ Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                    </span>
                  </td>
                  <td>{{ $notification->type }}</td>
                  <td>{{ $notification->title }}</td>
                  <td>{{ ($notification->read ? 'Read' : 'Unread') }}</td>
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
        <div class="pull-right">{{ $notifications->links() }}</div>
      </div><!-- /.box -->
    </div>
  </div>

@stop
