<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class EveCorporationKillMailDetail extends Eloquent {

	protected $table = 'corporation_killmail_detail';

	public function character()
	{
		return $this->hasMany('EveCorporationKillMails', 'corporationID', 'corporationID');
	}

    public function attackers()
    {
        return $this->hasMany('EveCorporationKillMailAttackers', 'killID', 'killID');
    }

    public function items()
    {
        return $this->hasMany('EveCorporationKillMailItems', 'killID', 'killID');
    }
}
