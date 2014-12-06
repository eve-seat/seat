<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class SeatSetting extends Eloquent {

	protected $table = 'seat_settings';

	public $primaryKey = 'setting';

}