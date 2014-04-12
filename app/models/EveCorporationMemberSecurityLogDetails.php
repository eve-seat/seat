<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class EveCorporationMemberSecurityLogDetails extends Eloquent {

	protected $table = 'corporation_msec_log_details';

	public function log()
	{
		return $this->hasOne('EveCorporationMemberSecurityLog', 'hash', 'hash');
	}
}
