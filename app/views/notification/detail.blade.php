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
                        <h4>Notification Type</h4>
                        <p>{{ $notification->type }}</p><br />
                        <h4>Notification Status</h4>
                        <p>{{ ($notification->read ? 'Read' : 'Unread') }}</p><br />
                        <h4>Notification Created</h4>
                        <p>{{ $notification->created_at->format('h:m:s / d-m-Y') }}</p><br />
                        <h4>Notification Text</h4>
                        <p>{{ $notification->text }}</p><br />
                        
                    </div>
                </div><!-- /.box-body -->
            </div><!-- /.box -->   

        <div class="col-md-6">
            <div class="box">

                <div class="box-header">
                    <h3 class="box-title">Actions</h3>
                </div>

                <div class="box-body">
                    @if($notification->read)
                         <a href="{{ action('NotificationController@getMarkUnread', array('notificationID' => $notification->id)) }}" class="btn btn-danger"><i class="fa fa-close"></i> Mark Notification Unread</a>
                    @else
                         <a href="{{ action('NotificationController@getMarkRead', array('notificationID' => $notification->id)) }}" class="btn btn-success"><i class="fa fa-check"></i> Mark Notification Read</a>
                    @endif
                </div><!-- /.box-body -->
            </div><!-- /.box -->        
        </div>
    </div>
@stop