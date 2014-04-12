<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class EveEveConquerableStationList extends Eloquent {

	protected $table = 'eve_conquerablestationlist';
	protected $fillable = array('stationID', 'stationName', 'stationTypeID', 'solarSystemID', 'corporationID', 'corporationName');
}
