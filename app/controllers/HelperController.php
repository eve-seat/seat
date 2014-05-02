<?php

use Seat\EveApi\BaseApi;
use Pheal\Pheal;

class HelperController extends BaseController {

	/*
	|--------------------------------------------------------------------------
	| postResolveNames()
	|--------------------------------------------------------------------------
	|
	| Take a list of ids and resolve them to in game names. We will store
	| already resolved names in the cache and just pick them up from there.
	| The remainder of the names will be queried by the API
	|
	*/

	public function postResolveNames()
	{

		// Create an array from the ids and make them unique
		$ids = explode(',', Input::get('ids'));
		$ids = array_unique($ids);

		// Set the array that we will eventually return, containing the resolved
		// names
		$return = array();

		// Start by doing a cache lookups for each of the ids to see what we have
		// already resolved
		foreach ($ids as $id) {
			
			if(Cache::has('nameid_' . $id)) {

				// Retreive the name from the cache and place it in the results.
				$return[$id] = Cache::get('nameid_' . $id);

				// Remove it from the $ids array as we don't need to lookup
				// this one.
				unset($ids[$id]);
			}
		}

		// Check if there is anything left to lookup, and prepare a pheal instance
		// to handle the API call for this
		if (count($ids) > 0) {

			// Get pheal ready
			BaseApi::bootstrap();
			$pheal = new Pheal();

			// Loop over the ids for a max of 30 ids, and resolve the names
			foreach (array_chunk($ids, 15) as $resolvable) {

				// Attempt actual API lookups
				try {

					$names = $pheal->eveScope->CharacterName(array('ids' => implode(',', $resolvable)));
					
				} catch (Exception $e) {

					throw $e;
				}

				// Add the results to the cache and the $return array
				foreach ($names->characters as $lookup_result) {
					
					Cache::forever('nameid_' . $lookup_result->characterID, $lookup_result->name);
					$return[$lookup_result->characterID] = $lookup_result->name;
				}
			}
		}

		// With all the work out of the way, return the $return array as Json
		return Response::json($return);
	}

	/*
	|--------------------------------------------------------------------------
	| getAvailableSkills()
	|--------------------------------------------------------------------------
	|
	| Return the currently available skills as a json object
	|
	*/

	public function getAvailableSkills()
	{

		// Get the skills from the database
		$skills = DB::table('invTypes')
			->select(DB::raw('typeId as id'), DB::raw('typeName as text'))
			->whereIn('groupID', function($groupQuery) {

				$groupQuery->select('groupID')
					->from('invGroups')
					->whereIn('categoryID', function($categoryQuery) {

						$categoryQuery->select('categoryID')
							->from('invCategories')
							->where('categoryName', 'skill');
					});
			})
			->orderBy('typeName', 'asc')
			->get();

		return Response::json($skills);
	}

	/*
	|--------------------------------------------------------------------------
	| getAvailableSkills()
	|--------------------------------------------------------------------------
	|
	| Return the currently available skills as a json object
	|
	*/

	public function getAvailableItems()
	{

		$items = DB::table('invTypes')
			->select(DB::raw('typeID as id'), DB::raw('typeName as text'))
			->where('typeName', 'like', '%' . Input::get('q') . '%')
			->get();

		return Response::json($items);
	}

	/*
	|--------------------------------------------------------------------------
	| getAvailablePeople()
	|--------------------------------------------------------------------------
	|
	| Return the currently available people by main names
	|
	*/

	public function getAvailablePeople()
	{

		$people = DB::table('seat_people_main')
			->select(DB::raw('personID as id'), DB::raw('characterName as text'))
			->where('characterName', 'like', '%' . Input::get('q') . '%')
			->get();

		return Response::json($people);
	}
}
