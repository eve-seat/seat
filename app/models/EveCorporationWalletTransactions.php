<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class EveCorporationWalletTransactions extends Eloquent {

    protected $guarded = array();

	protected $table = 'corporation_wallettransactions';
}
