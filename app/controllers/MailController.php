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

		return View::make('mail.timeline')
			->with('mail', $mail);
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

		return View::make('mail.read')
			->with('message', $message);
	}
}