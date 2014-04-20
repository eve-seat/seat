<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class EveEveRefTypes extends Eloquent {

	protected $table = 'eve_reftypes';
	protected $fillable = array('refTypeID', 'refTypeName');
	protected $primary = ('refTypeID');
}
