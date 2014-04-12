<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class EveCharacterAssetList extends Eloquent {

	protected $table = 'character_assetlist';

	public function contents()
	{
		return $this->hasMany('EveCharacterAssetListContents', 'itemID', 'itemID');
	}
}
