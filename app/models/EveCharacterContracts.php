<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class EveCharacterContracts extends Eloquent {

	protected $table = 'character_contracts';

	public function items()
	{
		return $this->hasMany('EveCharacterContractsItems', 'contractID', 'contractID');
	}
}
