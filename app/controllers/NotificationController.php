<?php

class NotificationController extends BaseController {

    /*
    |--------------------------------------------------------------------------
    | getShortStatus()
    |--------------------------------------------------------------------------
    |
    | Return the current notification count as json
    |
    */

    public function getShortStatus()
    {

        // Get the Queue information from the database
        $db_queue_count = \SeatNotification::where('user_id', '=', Sentry::getUser()->id)
            ->where('read', '=', '0')
            ->count();

        $response = array(
            'notification_count' => $db_queue_count
        );

        return Response::json($response);
    }

    /*
    |--------------------------------------------------------------------------
    | getStatus()
    |--------------------------------------------------------------------------
    |
    | Display the current notifications
    |
    */

    public function getStatus()
    {
        $notifications = \SeatNotification::where('user_id', '=', Sentry::getUser()->id)
            ->orderBy('id', 'DESC')
            ->get();
        
        $db_queue_count = \SeatNotification::where('user_id', '=', Sentry::getUser()->id)
            ->where('read', '=', '0')
            ->count();

        return View::make('notification.all')
            ->with('notifications', $notifications)
            ->with('unread', $db_queue_count);
    }

    /*
    |--------------------------------------------------------------------------
    | getDetail()
    |--------------------------------------------------------------------------
    |
    | Display the notification details
    |
    */

    public function getDetail($notificationID)
    {
        $notification = \SeatNotification::find($notificationID);

        if($notification->user_id == Sentry::getUser()->id) {
            $notification->read = 1;
            $notification->save();
            return View::make('notification.detail')
                ->with('notification', $notification);
        } else {
            App::abort(403);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | getMarkRead()
    |--------------------------------------------------------------------------
    |
    | Mark notification as read
    |
    */

    public function getMarkRead($notificationID)
    {
        $notification = \SeatNotification::find($notificationID);

        if($notification->user_id == Sentry::getUser()->id) {
            $notification->read = 1;
            $notification->save();
            return Redirect::action('NotificationController@getStatus')
                ->with('success', 'Notification "'.$notification->title.'" marked as read');
        } else {
            App::abort(403);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | getMarkUnread()
    |--------------------------------------------------------------------------
    |
    | Mark notification as unread
    |
    */

    public function getMarkUnread($notificationID)
    {
        $notification = \SeatNotification::find($notificationID);

        if($notification->user_id == Sentry::getUser()->id) {
            $notification->read = 0;
            $notification->save();
            return Redirect::action('NotificationController@getStatus')
                ->with('success', 'Notification "'.$notification->title.'" marked as unread');
        } else {
            App::abort(403);
        }
    }
}