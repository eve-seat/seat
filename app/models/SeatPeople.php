<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class SeatPeople extends Eloquent {

	protected $table = 'seat_people';

	public function main()
	{
		return $this->hasOne('SeatPeopleMain', 'personID', 'personID');
	}
}