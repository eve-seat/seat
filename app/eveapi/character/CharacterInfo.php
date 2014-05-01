<?php

namespace Seat\EveApi\Character;

use Seat\EveApi\BaseApi;
use Pheal\Pheal;

class Info extends BaseApi {

	/*
	|--------------------------------------------------------------------------
	| Seat\EveApi\Character\Info Update
	|--------------------------------------------------------------------------
	|
	| This is the only API call that does not follow the std procedure
	| that others are taking. Here, we are simply getting the characters
	| for a key, and passing the characterID along with the API key info
	| to Seat\EveApi\Eve\CharacterInfo for updating.
	|
	*/

	public static function Update($keyID, $vCode)
	{

		// Get the characters for this key
		$characters = BaseApi::findKeyCharacters($keyID);

		// Check if this key has any characters associated with it
		if (!$characters)
			return;

		// Next, start our loop over the characters and call Seat\EveApi\Eve\CharacterInfo
		// to handle the character updating
		foreach ($characters as $characterID)
			\Seat\EveApi\Eve\CharacterInfo::Update($characterID, $keyID, $vCode);

		return null;
	}
}
