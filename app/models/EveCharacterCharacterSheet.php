<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class EveCharacterCharacterSheet extends Eloquent {

	protected $table = 'character_charactersheet';

	public function skills()
	{
		return $this->hasMany('EveCharacterCharacterSheetSkills', 'characterID', 'characterID');
	}
}
