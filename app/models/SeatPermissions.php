<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class SeatPermissions extends Eloquent {

	protected $table = 'seat_permissions';

	public $primaryKey = 'permission';

	public $timestamps = false;

}