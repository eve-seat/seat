<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class EveCorporationContractsItems extends Eloquent {

	protected $table = 'corporation_contracts_items';

	public function contract()
	{
		return $this->hasOne('EveCorporationContracts', 'contractID', 'contractID');
	}
}
