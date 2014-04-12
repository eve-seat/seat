<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class EveEveAllianceList extends Eloquent {

	protected $table = 'eve_alliancelist';
	protected $fillable = array('name', 'shortName', 'allianceID', 'executorCorpID', 'memberCount', 'startDate');

	// Define the relationship with the MemberCorporations model.
	// Format: (related_to, foriegn_key, local_key)
    public function members() {

        return $this->hasMany('EveEveAllianceListMemberCorporations', 'allianceID', 'allianceID');
    }
}
