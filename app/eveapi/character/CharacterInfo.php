<?php
/*
The MIT License (MIT)

Copyright (c) 2014 eve-seat

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/

namespace Seat\EveApi\Character;

use Seat\EveApi\BaseApi;
use Pheal\Pheal;

class Info extends BaseApi
{

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
			\Seat\EveApi\Eve\CharacterInfo::Update((int)$characterID, $keyID, $vCode);

		return null;
	}
}
