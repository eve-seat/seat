<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class EveCharacterCharacterSheetJumpClones extends Eloquent {

	protected $table = 'character_charactersheet_jumpclones';

	public function owner()
	{
		return $this->belongsTo('EveCharacterCharacterSheet', 'characterID', 'characterID');
	}

    public function implants()
    {
        return $this->hasMany('EveCharacterCharacterSheetJumpCloneImplants', 'jumpCloneID', 'jumpCloneID');
    }
}
