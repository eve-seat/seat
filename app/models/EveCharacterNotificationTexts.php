<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class EveCharacterNotificationTexts extends Eloquent {

	protected $table = 'character_notification_texts';

	public function body()
	{
		return $this->hasMany('EveCharacterNotifications', 'notificationID', 'notificationID');
	}
}
