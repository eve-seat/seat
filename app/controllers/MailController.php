<?php

class MailController extends BaseController {

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

		$mail = DB::table('character_mailmessages')
			->join('character_mailbodies', 'character_mailmessages.messageID', '=', 'character_mailbodies.messageID')
			->orderBy('character_mailmessages.sentDate', 'desc')
			->paginate(100);

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

		$mail = DB::table('character_mailmessages')
			->join('character_mailbodies', 'character_mailmessages.messageID', '=', 'character_mailbodies.messageID')
			->groupby('character_mailmessages.messageID')
			->orderBy('character_mailmessages.sentDate', 'desc')
			->paginate(30);

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

		$mailing_list_names = array();
		foreach(DB::table('character_mailinglists')->get() as $list)
			$mailing_list_names[$list->listID] = $list->displayName;

		$recipients = DB::table('character_mailmessages')
			->where('messageID', $messageID)
			->lists('characterID');

		if(count($message) <= 0)
			return Redirect::action('MailController@getSubjects')
				->withErrors('Invalid Message ID');

		return View::make('mail.read')
			->with('message', $message)
			->with('mailing_list_names', $mailing_list_names)
			->with('recipients', $recipients);
	}
}