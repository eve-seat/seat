<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class EveEveCharacterInfo extends Eloquent {

	protected $table = 'eve_characterinfo';
	// protected $fillable = array('errorCode', 'errorText');

    public function employment() {

        return $this->hasMany('EveEveCharacterInfoEmploymentHistory', 'characterID', 'characterID');
    }
}
