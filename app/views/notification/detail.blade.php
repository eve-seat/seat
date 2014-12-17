@extends('layouts.masterLayout')

@section('html_title', 'Notification Detail #'.$notification->id)

@section('page_content')

  <div class="row">
    <div class="col-md-6">
      <div class="box">

        <div class="box-header">
          <h3 class="box-title">{{ $notification->title }}</h3>
        </div>

        <div class="box-body">

          <dl>
            <dt>Notification Type</dt>
            <dd>{{ $notification->type }}</dd>
            <dt>Notification Status</dt>
            <dd>{{ ($notification->read ? 'Read' : 'Unread') }}</dd>
            <dt>Notification Created</dt>
            <dd>{{ Carbon\Carbon::parse($notification->created_at)->diffForHumans() }} at {{ $notification->created_at }}</dd>
            <dt>Notification Text</dt>
            <dd>{{ $notification->text }}</dd>
          </dl>

        </div>
      </div><!-- /.box-body -->
    </div><!-- /.box -->

    <div class="col-md-6">
      <div class="box">

        <div class="box-header">
          <h3 class="box-title">Actions</h3>
        </div>

        <div class="box-body">
          <a href="{{ action('NotificationController@getAll') }}" class="btn btn-sm btn-default"><i class="fa fa-arrow-left"></i> Back to Notifications</a>
          @if($notification->read)
            <a href="{{ action('NotificationController@getMarkUnread', array('notificationID' => $notification->id)) }}" class="btn btn-sm btn-danger"><i class="fa fa-close"></i> Mark Notification Unread</a>
          @else
            <a href="{{ action('NotificationController@getMarkRead', array('notificationID' => $notification->id)) }}" class="btn btn-sm btn-success"><i class="fa fa-check"></i> Mark Notification Read</a>
          @endif
        </div><!-- /.box-body -->
      </div><!-- /.box -->
    </div>
  </div>
@stop
