<?php
/*
The MIT License (MIT)

Copyright (c) 2014 eve-seat

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/

class NotificationController extends BaseController
{

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
        $db_queue_count = \SeatNotification::where('user_id', '=', \Auth::User()->id)
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

    public function getAll()
    {

        $notifications = \SeatNotification::where('user_id', '=', \Auth::User()->id)
            ->orderBy('id', 'DESC')
            ->paginate(50);

        $unread_count = \SeatNotification::where('user_id', '=', \Auth::User()->id)
            ->where('read', '=', '0')
            ->count();

        return View::make('notification.all')
            ->with('notifications', $notifications)
            ->with('unread', $unread_count);
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

        if(!$notification)
            App::abort(404);

        if($notification->user_id == \Auth::User()->id) {

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
    | getMarkAllRead()
    |--------------------------------------------------------------------------
    |
    | Mark all notifications as read
    |
    */

    public function getMarkAllRead()
    {

        $notification = \SeatNotification::where('user_id', Auth::User()->id)
            ->update(array('read' => 1));

        return Redirect::action('NotificationController@getAll')
            ->with('success', $notification . ' notification(s) have been marked as read');

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

        if($notification->user_id == Auth::User()->id) {

            $notification->read = 1;
            $notification->save();

            return Redirect::action('NotificationController@getAll')
                ->with('success', 'Notification "' . $notification->title . '" marked as read');

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

        if($notification->user_id == Auth::User()->id) {

            $notification->read = 0;
            $notification->save();

            return Redirect::action('NotificationController@getAll')
                ->with('success', 'Notification "' . $notification->title . '" marked as unread');

        } else {

            App::abort(403);

        }
    }
}
