<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class EveMapKills extends Eloquent {

	protected $table = 'map_kills';
	protected $fillable = array('solarSystemID', 'shipKills', 'factionKills', 'podKills');
}
