<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class EveCharacterCharacterSheetImplants extends Eloquent {

	protected $table = 'character_charactersheet_implants';

	public function owner()
	{
		return $this->belongsTo('EveCharacterCharacterSheet', 'characterID', 'characterID');
	}
}
