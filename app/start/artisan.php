<?php

/*
|--------------------------------------------------------------------------
| Register The Artisan Commands
|--------------------------------------------------------------------------
|
| Each available Artisan command must be registered with the console so
| that it is available to be called. We'll register every command so
| the console gets access to each of the command object instances.
|
*/

// SeAT Commands
Artisan::add(new \Seat\Commands\SeatReset);
Artisan::add(new \Seat\Commands\SeatAPIUpdate);
Artisan::add(new \Seat\Commands\SeatQueueStatus);
Artisan::add(new \Seat\Commands\SeatAPIFindKeyByName);
Artisan::add(new \Seat\Commands\SeatAPIFindNameByKey);
Artisan::add(new \Seat\Commands\SeatAPIFindSickKeys);
Artisan::add(new \Seat\Commands\SeatAPIDeleteKey);
Artisan::add(new \Seat\Commands\SeatAddKey);
Artisan::add(new \Seat\Commands\SeatVersion);
Artisan::add(new \Seat\Commands\SeatDiagnose);
Artisan::add(new \Seat\Commands\SeatGroupSync);
Artisan::add(new \Seat\Commands\SeatClearCache);

// Scheduled SeAT Commands
Artisan::add(new \Seat\Commands\Scheduled\EveCharacterUpdater);
Artisan::add(new \Seat\Commands\Scheduled\EveCorporationAssetsUpdater);
Artisan::add(new \Seat\Commands\Scheduled\EveCorporationWalletUpdater);
Artisan::add(new \Seat\Commands\Scheduled\EveCorporationUpdater);
Artisan::add(new \Seat\Commands\Scheduled\EveEveUpdater);
Artisan::add(new \Seat\Commands\Scheduled\EveMapUpdater);
Artisan::add(new \Seat\Commands\Scheduled\EveServerUpdater);
Artisan::add(new \Seat\Commands\Scheduled\SeatQueueCleanup);
