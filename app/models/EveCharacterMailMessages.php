<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class EveCharacterMailMessages extends Eloquent {

	protected $table = 'character_mailmessages';

	public function body()
	{
		return $this->hasMany('EveCharacterMailBodies', 'messageID', 'messageID');
	}
}
