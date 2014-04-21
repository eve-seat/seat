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
	|
	*/
	
	public static function formatBigNumber($n) {
        // first strip any formatting;
        $n = (0+str_replace(",","",$n));
       
        // is this a number?
        if(!is_numeric($n)) return false;
       
        // now filter it;
        if($n>1000000000000) return round(($n/1000000000000),1).'t';
        else if($n>1000000000) return round(($n/1000000000),1).'b';
        else if($n>1000000) return round(($n/1000000),1).'m';
        else if($n>1000) return round(($n/1000),1).'k';
       
        return number_format($n);
    }
}