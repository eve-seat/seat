<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| SeAT Version
	|--------------------------------------------------------------------------
	|
	*/

	'version' => '0.7',

	/*
	|--------------------------------------------------------------------------
	| Ban Limit
	|--------------------------------------------------------------------------
	|
	| Specifies the amount of times a API call should fail before it should
	| should be banned from being called again
	|
	*/

	'ban_limit' => 10,

	/*
	|--------------------------------------------------------------------------
	| Ban Grance Period
	|--------------------------------------------------------------------------
	|
	| Specifies the amount of minutes a key should live in the cache when
	| counting the bans for it.
	|
	| It is important to note that the actual schedule at which API calls are
	| made should be taken into account when setting this Value. ie: For
	| character API Updates, which occur hourly, it would take 10 hours
	| to reach the limit and get banned. If we set the ban_grace key
	| annything below 600, we will never reach a point where a ban
	| will occur
	|
	*/

	'ban_grace' => 60 * 24,

	/*
	|--------------------------------------------------------------------------
	| Allow Registrations
	|--------------------------------------------------------------------------
	|
	|	Specify whether the website is currently allow registrations 
	|
	*/

	'allow_registration' => false,

);