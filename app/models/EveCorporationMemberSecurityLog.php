<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class EveCorporationMemberSecurityLog extends Eloquent {

	protected $table = 'corporation_msec_log';

	public function details()
	{
		return $this->hasMany('EveCorporationMemberSecurityLogDetails', 'hash', 'hash');
	}
}
