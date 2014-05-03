<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class SeatPeopleMain extends Eloquent {

	protected $table = 'seat_people_main';

	public function person()
	{
		return $this->belongsTo('SeatPeople', 'personID', 'personID');
	}
}