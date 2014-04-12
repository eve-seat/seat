<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class EveMapJumps extends Eloquent {

	protected $table = 'map_jumps';
	protected $fillable = array('solarSystemID', 'shipJumps');
}
