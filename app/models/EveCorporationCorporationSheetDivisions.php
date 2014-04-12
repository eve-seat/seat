<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class EveCorporationCorporationSheetDivisions extends Eloquent {

	protected $table = 'corporation_corporationsheet_divisions';

	public function corporation()
	{
		return $this->hasOne('EveCorporationCorporationSheet', 'corporationID', 'corporationID');
	}
}
