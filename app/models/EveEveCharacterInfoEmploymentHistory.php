<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class EveEveCharacterInfoEmploymentHistory extends Eloquent {

	protected $table = 'eve_characterinfo_employmenthistory';
	protected $fillable = array('recordID', 'corporationID', 'startDate');

    public function character() 
    {
        return $this->belongsTo('EveEveCharacterInfo');
    }
}
