<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class EveCharacterWalletJournal extends Eloquent {

    protected $guarded = array();

	protected $table = 'character_walletjournal';
}
