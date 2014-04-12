<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class EveCharacterCharacterSheetSkills extends Eloquent {

	protected $table = 'character_charactersheet_skills';

	public function owner()
	{
		return $this->belongsTo('EveCharacterCharacterSheet', 'characterID', 'characterID');
	}
}
