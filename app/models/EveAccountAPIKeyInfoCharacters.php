<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class EveAccountAPIKeyInfoCharacters extends Eloquent {

	protected $table = 'account_apikeyinfo_characters';

    public function key() {

        return $this->hasOne('EveAccountAPIKeyInfo');
    }
}
