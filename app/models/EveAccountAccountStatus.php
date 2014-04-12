<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class EveAccountAccountStatus extends Eloquent {

	protected $table = 'account_accountstatus';

    public function keyInfo() {

        return $this->hasOne('EveAccountAPIKeyInfo', 'keyID', 'keyID');
    }
}
