<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class EveCorporationWalletJournal extends Eloquent {

    protected $guarded = array();

	protected $table = 'corporation_walletjournal';
}
