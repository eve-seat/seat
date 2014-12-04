<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class EveCharacterKillMailDetail extends Eloquent {

	protected $table = 'character_killmail_detail';

	public function character()
	{
		return $this->hasMany('EveCharacterKillMails', 'characterID', 'characterID');
	}

    public function attackers()
    {
        return $this->hasMany('EveCharacterKillMailAttackers', 'killID', 'killID');
    }

    public function items()
    {
        return $this->hasMany('EveCharacterKillMailItems', 'killID', 'killID');
    }
}
