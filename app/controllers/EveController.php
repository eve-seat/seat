<?php

use Seat\EveApi\BaseApi;
use Pheal\Pheal;

class EveController extends BaseController {

/*
	|--------------------------------------------------------------------------
	| getSearchItems()
	|--------------------------------------------------------------------------
	|
	| Return a view to search items
	|
	*/

	public function getSearchItems()
	{
		
		return View::make('eve.item.search');
	}

	/*
	|--------------------------------------------------------------------------
	| postSearchItems()
	|--------------------------------------------------------------------------
	|
	| Return a view to show search items result
	|
	*/

	public function postSearchItems()
	{

		if (!is_array(Input::get('items')))
			App::abort(404);

		$item_list = Input::get('items');

		$items = DB::table('invTypes')->whereIn('invTypes.typeID', array_values($item_list))->get();

		return View::make('eve.item.ajax.result')
			->with('items', $items);
	}
}
