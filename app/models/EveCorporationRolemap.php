<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class EveEveNotificationTypes extends Eloquent {

    protected $table = 'eve_corporation_rolemap';
    protected $fillable = array('roleID', 'roleName');
}
