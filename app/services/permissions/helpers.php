<?php

namespace App\Services\Permissions;
 
class PermissionHelper {

	/*
	|--------------------------------------------------------------------------
	| hasDirector()
	|--------------------------------------------------------------------------
	|
	| Check if the currently logged in user has any director roles
	|
	*/
 
	public static function hasDirector() {

		$directorship = \Session::get('is_director');

		if (empty($directorship))
			return false;
		else
			return true;
    }
}