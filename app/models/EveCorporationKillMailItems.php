<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class EveCorporationKillMailItems extends Eloquent {

	protected $table = 'corporation_killmail_items';

	public function killmail()
	{
		return $this->belongsTo('EveCorporationKillMailDetail', 'killID', 'killID');
	}
}
