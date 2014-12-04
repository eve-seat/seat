<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class EveCharacterKillMailAttackers extends Eloquent {

	protected $table = 'character_killmail_attackers';

	public function killmail()
	{
		return $this->belongsTo('EveCharacterKillMailDetail', 'killID', 'killID');
	}
}
