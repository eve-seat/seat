<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class EveCharacterNotifications extends Eloquent {

	protected $table = 'character_notifications';

	public function body()
	{
		return $this->hasOne('EveCharacterNotificationTexts', 'notificationID', 'notificationID');
	}
}
