<?php

class HelpController extends BaseController {

	/*
	|--------------------------------------------------------------------------
	| Help Controller
	|--------------------------------------------------------------------------
	|
	*/

	public function getHelp()
	{


		// Get the current version
		$current_version = \Config::get('seat.version');
		// Try determine how far back we are on releases
		$versions_behind = 0;

		try {

			// Check the releases from Github for eve-seat/seat
			$headers = array('Accept' => 'application/json');
			$request = Requests::get('https://api.github.com/repos/eve-seat/seat/releases', $headers);

			if ($request->status_code == 200) {

				$release_data = json_decode($request->body);

				// Try and determine if we are up to date
				if ($release_data[0]->tag_name == 'v'.$current_version) {

				} else {

					foreach ($release_data as $release) {

						if ($release->tag_name == 'v'.$current_version)
							break;
						else
							$versions_behind++;
					}
				}

			} else {
				$release_data = null;
			}

		} catch (Exception $e) {

			$release_data = null;
		}

		return View::make('help.help')
			->with('release_data', $release_data)
			->with('versions_behind', $versions_behind);
	}
}