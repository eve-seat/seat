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
	
 		if($id > 90000000 && $id < 98000000){
			return 'http://image.eveonline.com/Character/'.$id.'_'.$size.'.jpg';
		} elseif($id > 98000000 && $id < 99000000){
			return 'http://image.eveonline.com/Corporation/'.$id.'_'.$size.'.png';
		} elseif($id > 99000000 && $id < 100000000){
			return 'http://image.eveonline.com/Alliance/'.$id.'_'.$size.'.png';
		} else {
			return 'http://image.eveonline.com/Character/'.$id.'_'.$size.'.jpg';
		}
	}
}