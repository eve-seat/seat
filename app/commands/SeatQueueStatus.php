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

namespace Seat\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use Carbon\Carbon;

class SeatQueueStatus extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'seat:queue-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Query and return information about the current queues.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {

        // Connect to the Redis backend using Predis
        $redis = new \Predis\Client(array('host' => \Config::get('database.redis.default.host'), 'port' => \Config::get('database.redis.default.port')));

        // Count the amount of jobs in the queue by
        // issuing a Redis LRANGE command:
        //
        // http://redis.io/commands/LRANGE
        $redis_count = count($redis->lrange('queues:default', 0, -1));
        $this->line('Redis reports ' . $redis_count . ' jobs in the queue (queues:default)');

        // Get the Queued job count from the database
        $db_info = \SeatQueueInformation::where('status', '=', 'Queued')->count();
        $this->line('The database reports ' . $db_info . ' queued jobs');

        // If the amount of jobs in the redis queue does
        // not match that of the database, just warn
        // about this.
        if ($db_info <> $redis_count)
            $this->info('[!] The redis & db queued counts are not the same. This is not always a bad thing');

        // Get the Done job count frmo the database
        $db_info = \SeatQueueInformation::where('status', '=', 'Done')->count();
        $this->line('The database reports ' . $db_info . ' done jobs');

        // Get the Error job count from the database
        $db_info = \SeatQueueInformation::where('status', '=', 'Error')->count();

        // If there are Error jobs, loop over them and
        // print the status details
        if ($db_info > 0) {

            $this->comment('Current error-state jobs (' . $db_info . '):');

            foreach (\SeatQueueInformation::where('status', '=', 'Error')->get() as $row) {

                $this->line(
                    'OwnerID: ' . $row->ownerID . ' | Scope: ' . $row->scope .
                    ' | API: ' . $row->api . ' | Status: "' . str_limit($row->output, 20, '...') .
                    '" | Created: ' . Carbon::parse($row->created_at)->diffForHumans() .
                    ' | Last updated: ' . Carbon::parse($row->updated_at)->diffForHumans()
                );
            }

        } else {

            $this->line('The database reports ' . $db_info . ' error jobs');

        }

        // Get the Working jobs from the database
        $db_info = \SeatQueueInformation::where('status', '=', 'Working')->count();
        if ($db_info > 0) {

            $this->comment('Current working-state jobs (' . $db_info . '):');

            foreach (\SeatQueueInformation::where('status', '=', 'Working')->get() as $row) {

                $this->line(
                    'OwnerID: ' . $row->ownerID . ' | Scope: ' . $row->scope .
                    ' | API: ' . $row->api . ' | Status: "' . $row->output .
                    '" | Created: ' . Carbon::parse($row->created_at)->diffForHumans() .
                    ' | Last updated: ' . Carbon::parse($row->updated_at)->diffForHumans()
                );
            }

        } else {

            $this->line('The database reports ' . $db_info . ' working jobs');
        }
    }
}
