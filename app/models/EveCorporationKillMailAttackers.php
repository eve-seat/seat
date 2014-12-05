<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class EveCorporationKillMailAttackers extends Eloquent {

	protected $table = 'corporation_killmail_attackers';

	public function killmail()
	{
		return $this->belongsTo('EveCorporationKillMailDetail', 'killID', 'killID');
	}
}
