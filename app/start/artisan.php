<?php
/*
The MIT License (MIT)

Copyright (c) 2014 eve-seat

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/

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
Artisan::add(new \Seat\Commands\SeatUpdateSDE);
Artisan::add(new \Seat\Commands\SeatInstall);
Artisan::add(new \Seat\Commands\SeatUserMigrate);

// Scheduled SeAT Commands
Artisan::add(new \Seat\Commands\Scheduled\EveCharacterUpdater);
Artisan::add(new \Seat\Commands\Scheduled\EveCorporationAssetsUpdater);
Artisan::add(new \Seat\Commands\Scheduled\EveCorporationWalletUpdater);
Artisan::add(new \Seat\Commands\Scheduled\EveCorporationUpdater);
Artisan::add(new \Seat\Commands\Scheduled\EveEveUpdater);
Artisan::add(new \Seat\Commands\Scheduled\EveMapUpdater);
Artisan::add(new \Seat\Commands\Scheduled\EveServerUpdater);
Artisan::add(new \Seat\Commands\Scheduled\SeatQueueCleanup);
Artisan::add(new \Seat\Commands\Scheduled\SeatNotify);
