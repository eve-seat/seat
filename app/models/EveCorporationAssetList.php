<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class EveCorporationAssetList extends Eloquent {

	protected $table = 'corporation_assetlist';

	public function contents()
	{
		return $this->hasMany('EveCorporationAssetListContents', 'itemID', 'itemID');
	}
}
