<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class EveCorporationKillMails extends Eloquent {

	protected $table = 'corporation_killmails';

	public function detail()
	{
		return $this->hasOne('EveCorporationKillMailDetail', 'killID', 'killID');
	}
}
