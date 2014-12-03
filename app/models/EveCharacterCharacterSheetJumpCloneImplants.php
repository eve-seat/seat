<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class EveCharacterCharacterSheetJumpCloneImplants extends Eloquent {

	protected $table = 'character_charactersheet_jumpclone_implants';

	public function jumpClone()
	{
		return $this->belongsTo('EveCharacterCharacterSheetJumpClones', 'jumpCloneID', 'jumpCloneID');
	}
}
