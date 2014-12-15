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

class MailController extends BaseController
{

    /*
    |--------------------------------------------------------------------------
    | getSubjects()
    |--------------------------------------------------------------------------
    |
    | Display all of the mails in the system by message subject
    |
    */

    public function getSubjects()
    {

        $pagination_amount = 100;

        $mail = DB::table('character_mailmessages')
            ->join('account_apikeyinfo_characters', 'character_mailmessages.characterID', '=', 'account_apikeyinfo_characters.characterID')
            ->join('character_mailbodies', 'character_mailmessages.messageID', '=', 'character_mailbodies.messageID')
            ->orderBy('character_mailmessages.sentDate', 'desc');

        if (!\Auth::isSuperUser() )
            $mail = $mail->whereIn('account_apikeyinfo_characters.keyID', Session::get('valid_keys'));

        $mail = $mail->paginate($pagination_amount);

        return View::make('mail.subjects')
            ->with('mail', $mail);
    }

    /*
    |--------------------------------------------------------------------------
    | getTimeline()
    |--------------------------------------------------------------------------
    |
    | Display all of the mails in the system with the full body, sorted by
    | sent date.
    |
    */

    public function getTimeline()
    {

        $pagination_amount = 30;

        $mail = DB::table('character_mailmessages')
            ->join('account_apikeyinfo_characters', 'character_mailmessages.characterID', '=', 'account_apikeyinfo_characters.characterID')
            ->join('character_mailbodies', 'character_mailmessages.messageID', '=', 'character_mailbodies.messageID')
            ->groupby('character_mailmessages.messageID')
            ->orderBy('character_mailmessages.sentDate', 'desc');

        if (!\Auth::isSuperUser() )
            $mail = $mail->whereIn('account_apikeyinfo_characters.keyID', Session::get('valid_keys'));

        $mail = $mail->paginate($pagination_amount);

        $mailing_list_names = array();
        foreach(DB::table('character_mailinglists')->get() as $list)
            $mailing_list_names[$list->listID] = $list->displayName;

        return View::make('mail.timeline')
            ->with('mail', $mail)
            ->with('mailing_list_names', $mailing_list_names);
    }

    /*
    |--------------------------------------------------------------------------
    | getRead()
    |--------------------------------------------------------------------------
    |
    | Display a single mail message
    |
    */

    public function getRead($messageID = 0)
    {
        $message = DB::table('character_mailmessages')
            ->join('character_mailbodies', 'character_mailmessages.messageID', '=', 'character_mailbodies.messageID')
            ->where('character_mailmessages.messageID', $messageID)
            ->first();

        if(count($message) <= 0)
            return Redirect::action('MailController@getSubjects')
                ->withErrors('Invalid Message ID');

        $recipients = DB::table('character_mailmessages')
            ->where('messageID', $messageID)
            ->lists('characterID');

        // Ensure that the current user is allowed to view the mail
        if (!\Auth::isSuperUser() ) {

            // Get all the keys that have this mail recorded
            $keys_with_mail = DB::table('character_mailmessages')
                ->join('account_apikeyinfo_characters', 'character_mailmessages.characterID', '=', 'account_apikeyinfo_characters.characterID')
                ->where('messageID', $messageID)
                ->whereIn('account_apikeyinfo_characters.keyID', Session::get('valid_keys'))
                ->first();

            // If we are unable to find a key with the mail that this user has access to, 404
            if (!$keys_with_mail)
                App::abort(404);
        }

        $mailing_list_names = array();
        foreach(DB::table('character_mailinglists')->get() as $list)
            $mailing_list_names[$list->listID] = $list->displayName;

        return View::make('mail.read')
            ->with('message', $message)
            ->with('mailing_list_names', $mailing_list_names)
            ->with('recipients', $recipients);
    }
}
