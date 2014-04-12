<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class EveMapSovereignty extends Eloquent {

	protected $table = 'map_sovereignty';
	protected $fillable = array('solarSystemID', 'allianceID', 'factionID', 'solarSystemName', 'corporationID');
}
