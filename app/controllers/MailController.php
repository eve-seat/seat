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

		$pagination_amount = 100;

		$mail = DB::table('character_mailmessages')
			->join('account_apikeyinfo_characters', 'character_mailmessages.characterID', '=', 'account_apikeyinfo_characters.characterID')
			->join('character_mailbodies', 'character_mailmessages.messageID', '=', 'character_mailbodies.messageID')
			->orderBy('character_mailmessages.sentDate', 'desc');

		if (!Sentry::getUser()->isSuperUser())
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

		if (!Sentry::getUser()->isSuperUser())
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
		if (!Sentry::getUser()->isSuperUser()) {

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