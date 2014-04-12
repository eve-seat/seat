<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class EveCharacterAssetListContents extends Eloquent {

	protected $table = 'character_assetlist_contents';

	public function from()
	{
		return $this->hasOne('EveCharacterAssetList', 'itemID', 'itemID');
	}
}
