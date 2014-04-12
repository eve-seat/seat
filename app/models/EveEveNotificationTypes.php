<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class EveEveNotificationTypes extends Eloquent {

	protected $table = 'eve_notification_types';
	protected $fillable = array('typeID', 'description');
}
