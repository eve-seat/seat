<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class SeatQueueInformation extends Eloquent {

	protected $table = 'queue_information';
	protected $fillable = array('jobID', 'ownerID', 'api', 'scope', 'type');
}
