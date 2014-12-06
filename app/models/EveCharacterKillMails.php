<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class EveCharacterKillMails extends Eloquent {

	protected $table = 'character_killmails';

	public function detail()
	{
		return $this->hasOne('EveCharacterKillMailDetail', 'killID', 'killID');
	}
}
