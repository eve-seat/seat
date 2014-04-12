<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class EveCharacterMailBodies extends Eloquent {

	protected $table = 'character_mailbodies';

	public function header()
	{
		return $this->hasOne('EveCharacterCharacterSheetSkills', 'messageID', 'messageID');
	}
}
