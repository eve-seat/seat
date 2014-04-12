<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class EveEveAllianceListMemberCorporations extends Eloquent {

	protected $table = 'eve_alliancelist_membercorporations';
	protected $fillable = array('name', 'shortName', 'allianceID', 'executorCorpID', 'memberCount', 'startDate');

    public function alliance() 
    {
        return $this->belongsTo('EveEveAllianceList');
    }
}
