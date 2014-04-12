<?php

namespace Seat\EveApi\Api;

use Seat\EveApi\BaseApi;
use Pheal\Pheal;

class Calllist extends BaseApi {


	public static function Update()
	{
		BaseApi::bootstrap();

		$pheal = new Pheal();
		$calllist = $pheal->apiScope->calllist();

		return $calllist;
	}
}
