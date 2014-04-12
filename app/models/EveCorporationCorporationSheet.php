<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class EveCorporationCorporationSheet extends Eloquent {

	protected $table = 'corporation_corporationsheet';

	public function divisions()
	{
		return $this->hasMany('EveCorporationCorporationSheetDivisions', 'corporationID', 'corporationID');
	}

	public function walletdivisions()
	{
		return $this->hasMany('EveCorporationCorporationSheetDivisions', 'corporationID', 'corporationID');
	}
}
