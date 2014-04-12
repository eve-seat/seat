<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class EveCorporationContracts extends Eloquent {

	protected $table = 'corporation_contracts';

	public function items()
	{
		return $this->hasMany('EveCorporationContractsItems', 'contractID', 'contractID');
	}
}
