<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class EveCorporationAssetListContents extends Eloquent {

	protected $table = 'corporation_assetlist_contents';

	public function from()
	{
		return $this->hasOne('EveCorporationAssetList', 'itemID', 'itemID');
	}
}
