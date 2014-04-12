<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class EveCorporationCorporationSheetWalletDivisions extends Eloquent {

	protected $table = 'corporation_corporationsheet_walletdivisions';

	public function corporation()
	{
		return $this->hasOne('EveCorporationCorporationSheet', 'corporationID', 'corporationID');
	}
}
