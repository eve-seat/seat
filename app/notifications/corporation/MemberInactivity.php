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

namespace Seat\Notifications\Corporation;

use Seat\Notifications\BaseNotify;

/*
|--------------------------------------------------------------------------
| Corporation Member Inactivity Notification
|--------------------------------------------------------------------------
|
| This notification looks up corporation members and alerts about them
| being inactive if they have not been active for more than 1 month
|
*/

class MemberInactivity extends BaseNotify
{

    public static function Update()
    {

        // Before we even bother getting to the point of
        // determining which members are inactive
        // check if there are any users
        // configured that have
        // the required roles
        $recruiters = \Auth::findAllUsersWithAccess('recruiter');

        // If there are none with the role, just stop
        if(count($recruiters) <= 0)
            return;

        // Now we will loop over the recruiters and send
        // a notification about the member inactivity.
        // We should actually batch the members so
        // that we dont spam a 100 entries per
        // recruiter for inactivities. But
        // that can come later :D
        foreach($recruiters as $recruiter) {

            // Now, get a list of members that have not
            // logged in for 1 month.
            $members = \EveCorporationMemberTracking::where('logoffDateTime', '<', \DB::raw('date_sub(NOW(), INTERVAL 1 MONTH)'))->get();

            // If there are no members, continue
            if (!$members)
                continue;

            // For every member that we have found, we want
            // to send a notification for.
            foreach($members as $member){

                // Determine if this recruiter that we have
                // has access to the corporation that the
                // member is a part of.
                //
                // Is this step really needed?
                if(BaseNotify::canAccessCorp($recruiter->id, $member->corporationID)) {

                    // We will make sure that we don't spam the user
                    // with notifications, so lets hash the event
                    // and ensure that its not present in the
                    // database yet

                    // Prepare the total notification
                    $notification_type = 'Corporation';
                    $notification_title = 'Member Inactivity';
                    $notification_text = $member->name . ' has been inactive for over a month and last logged' .
                            ' in ' . \Carbon\Carbon::parse($member->logonDateTime)->diffFOrHumans();

                    // Send the notification
                    BaseNotify::sendNotification($recruiter->id, $notification_type, $notification_title, $notification_text);

                }
            }
        }
    }
}
