<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class EveCharacterKillMailItems extends Eloquent {

	protected $table = 'character_killmail_items';

	public function killmail()
	{
		return $this->belongsTo('EveCharacterKillMailDetail', 'killID', 'killID');
	}
}
