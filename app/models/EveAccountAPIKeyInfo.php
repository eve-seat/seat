<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class EveAccountAPIKeyInfo extends Eloquent {

	protected $table = 'account_apikeyinfo';

    public function characters() {

        return $this->hasMany('EveAccountAPIKeyInfoCharacters', 'keyID', 'keyID');
    }

    public function accountStatus() {

        return $this->hasOne('EveAccountAccountStatus', 'keyID', 'keyID');
    }

    public function key()
    {
    	return $this->belongsTo('SeatKey', 'keyID', 'keyID');
    }
}
