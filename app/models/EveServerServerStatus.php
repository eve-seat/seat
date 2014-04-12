<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class EveServerServerStatus extends Eloquent {

	protected $table = 'server_serverstatus';
	protected $fillable = array('currentTime', 'serverOpen', 'onlinePlayers');
}