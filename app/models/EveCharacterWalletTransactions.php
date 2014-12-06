<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class EveCharacterWalletTransactions extends Eloquent {

    protected $guarded = array();

	protected $table = 'character_wallettransactions';
}
