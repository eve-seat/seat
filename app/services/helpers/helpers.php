<?php

namespace App\Services\Helpers;
 
class Helpers {

	/*
	|--------------------------------------------------------------------------
	| findSkillLevel()
	|--------------------------------------------------------------------------
	|
	| Take a the characters skills array and search for the level at which
	| a certain skillID is.
	|
	*/
 
	public static function findSkillLevel($character_skills, $skillID) {

		foreach ($character_skills as $skill_group) {

			foreach ($skill_group as $skill) {

				if ($skill['typeID'] == $skillID)
					return $skill['level'];
			}
		}

		// If we can not find the skill, then it is 0
		return 0;
    }

	/*
	|--------------------------------------------------------------------------
	| processAccessMask()
	|--------------------------------------------------------------------------
	|
	| Run through the api calllist, checking which calls a accessmask has
	| access to
	|
	*/

	public static function processAccessMask($mask, $type) {

		// Prepare the return array
		$return = array();

		// Loop over the call list, populating the array, based on the type
		if ($type == 'Corporation') {

			// Loop over the call list, populating the array
			foreach (\EveApiCalllist::where('type', 'Corporation')->get() as $call)
				$return[$call->name] = (int)$call->accessMask & (int)$mask ? 'true' : 'false';

		} else {

			// Loop over the call list, populating the array
			foreach (\EveApiCalllist::where('type', 'Character')->get() as $call)
				$return[$call->name] = (int)$call->accessMask & (int)$mask ? 'true' : 'false';
		}

		// Return the populated array
		return $return;
    }
	
	/*
	|--------------------------------------------------------------------------
	| formatBigNumber()
	|--------------------------------------------------------------------------
	|
	| Format a number to condesed format with suffix
	|
	*/
	
	public static function formatBigNumber($number) {

        // first strip any formatting;
        $number = (0 + str_replace(',', '', $number));
       
        // is this a number?
        if(!is_numeric($number))
        	return false;

        $places = 0;
       
        // now filter it;
        if($number > 1000000000000)
        	return round(($number / 1000000000000), 1) . 't';

        else if($number > 1000000000)
        	return round(($number / 1000000000), 1) . 'b';

        else if($number > 1000000)
        	return round(($number / 1000000), 1) . 'm';

        else if($number > 1000)
        	return round(($number / 1000), 1) . 'k';

        else if($number > 100)
        	$places = 2;
        	return $number;
       
        return number_format($number, $places);
    }
	
	/*
	|--------------------------------------------------------------------------
	| generateEveImage()
	|--------------------------------------------------------------------------
	|
	| Return the URL of image for a given ID and check if it's a character, 
	| 	corporation or Alliance ID
	| There no way to find if id is character, corporation or alliance before 
	| 	the 64bits move. For that if the ID given is not in range we consider
	|	it's a character ID.
	| From here: https://forums.eveonline.com/default.aspx?g=posts&m=716708#post716708
	| Valid size is: 32, 64, 128, 256, 512
	| TODO: Find a way to fix the id before 64bits move.
	|
	*/
	
	public static function generateEveImage($id, $size) {
	
 		if($id > 90000000 && $id < 98000000) {

			return '//image.eveonline.com/Character/' . $id . '_' . $size . '.jpg';

		} elseif(($id > 98000000 && $id < 99000000) || ($id > 1000000 && $id < 2000000)) {

			return '//image.eveonline.com/Corporation/' . $id . '_' . $size . '.png';

		} elseif(($id > 99000000 && $id < 100000000) || ($id > 0 && $id < 1000000)) {

			return '//image.eveonline.com/Alliance/' . $id . '_' . $size . '.png';

		} else {

			return '//image.eveonline.com/Character/' . $id . '_' . $size . '.jpg';
		}
	}

    /*
    |--------------------------------------------------------------------------
    | parseCorpSecurityRoleLogs()
    |--------------------------------------------------------------------------
    |
    | Return the Roles from corporation member security-log table
    | TODO: More Documentation.
    |
    */

    public static function parseCorpSecurityRoleLog($roleString) {
        if($roleString == "[]" and is_string($roleString)) {
            return "";
        } elseif ($roleString <> "" and is_string($roleString)) {
            $t = implode(', ',get_object_vars(json_decode($roleString)));
            return str_replace("role","",$t);
        }

    }

    /*
	|--------------------------------------------------------------------------
	| makePrettyMemberRoleList()
	|--------------------------------------------------------------------------
	|
	| Returns a pretty Corporation Member Role List
	| TODO: More Documentation.
	|
	*/

	public static function makePrettyMemberRoleList($stringToFormat) {
		if($stringToFormat == "" or is_null ($stringToFormat)) {
			return "";
		} else  {
			return str_replace(",",", ",$stringToFormat);
		}

	}

	/*
	|--------------------------------------------------------------------------
	| sumVolume()
	|--------------------------------------------------------------------------
	|
	| Returns the total volume of an array of assets
	| param $personal is passed if the assets is calculated for the person
	| 	and not the corporation
	|
	*/

	public static function sumVolume($array, $col_name, $personal = NULL) {
		$volume = 0;
		foreach($array as $item){
			if(isset($item['contents'])){
				foreach($item['contents'] as $type){
					if($personal)
						$volume += ($type[$col_name] * $type['quantity']);
					else
						$volume += $type[$col_name];
				}
			}
			if($personal)
				$volume += ($item[$col_name] * $item['quantity']);
			else
				$volume += $item[$col_name];
		}
		return Helpers::formatBigNumber($volume);
	}

	/*
	|--------------------------------------------------------------------------
	| numAssets()
	|--------------------------------------------------------------------------
	|
	| Returns the number of assets including what is within a container
	|
	|
	*/

	public static function numAssets($array) {
		$count = 0;
		foreach($array as $item){
			$count++;
			if(isset($item['contents'])){
				foreach($item['contents'] as $type){
					$count++;
				}
			}
		}		
		return $count;
	}

	/*
	|--------------------------------------------------------------------------
	| marketOrderType()
	|--------------------------------------------------------------------------
	|
	| Returns the total of a market order type, ie:
	| Pass in an array of market orders, with the type (col_name)
	| you are looking for and an integer will be provided for all the 
	| orders in that state within the array
	| 
	| '0' => 'Active',
	| '1' => 'Closed',
	| '2' => 'Expired / Fulfilled',
	| '3' => 'Cancelled',
	| '4' => 'Pending',
	| '5' => 'Deleted'
	|
	*/

	public static function marketOrderCount($array, $col_name) {
		$count = 0;
		foreach($array as $order){
			if($order->orderState == $col_name){
				$count ++;
			}
		}
		return $count;
	}
	
}
