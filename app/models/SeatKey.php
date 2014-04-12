<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class SeatKey extends Eloquent {

	public function user()
	{
		return $this->belongsTo('User');
	}

	public function apiKeyInfo()
	{
		return $this->hasOne('EveAccountAPIKeyInfo', 'keyID', 'keyID');
	}
}