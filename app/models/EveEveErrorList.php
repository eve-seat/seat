<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class EveEveErrorList extends Eloquent {

	protected $table = 'eve_errorlist';
	protected $fillable = array('errorCode', 'errorText');
}
