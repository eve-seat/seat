<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class EveCharacterContractsItems extends Eloquent {

	protected $table = 'character_contracts_items';

	public function contract()
	{
		return $this->hasOne('EveCharacterContracts', 'contractID', 'contractID');
	}
}
