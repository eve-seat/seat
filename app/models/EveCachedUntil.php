<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class EveCachedUntil extends Eloquent {

	protected $table = 'cached_until';
	protected $fillable = array('ownerID', 'api', 'scope', 'hash', 'cached_until');
}
