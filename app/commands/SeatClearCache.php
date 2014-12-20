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

class SeatClearCache extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'seat:clear-cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clears all the api caches. This includes Redis, the DB caching and the Pheal disk-cache';

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

        // Start with some information about what we are going to do here
        $this->line('This command will attempt to clear all of the cache services in use with SeAT.');
        $this->line('This only time that that this command should really be used is to aid developers in debugging.');
        $this->line('');
        $this->line('Ensure that the user invoking this command is allowed to access the cache directory.');

        // Be annoying and ask if the user is sure he wants to clear all caches
        if ($this->confirm('Are you sure you want to clear ALL caches (file/db/redis)? [yes|no]', true)) {

            $this->line('Clearing caches...');

            // Attempt cleanup of the pheal-ng disk cache.
            $success = \File::deleteDirectory(storage_path(). '/cache/phealcache', true);

            // If we failed to delete the contents of the cache directort,
            // let the user know how to delete this manually.
            if(!$success) {

                $this->error('Warning: Failed to delete pheal-ng disk cache! It may mean that the current user does not have the required permissions.');
                $this->error('You may manually attempt cleanup of this by deleting the contents of ' . storage_path(). '/cache/phealcache/');

            } else {

                $this->info('Pheal-ng disk cache cleared.');

                // As we have just wiped the contents, we have to put back the .gitignore.
                // Maybe there is a better way to do this, I don't know ^_^
                $gitignore_contents = "*\n!.gitignore";
                $file_write = \File::put(storage_path(). '/cache/phealcache/.gitignore', $gitignore_contents);

                // Check if this was successfull
                if ($file_write === false)
                    $this->error('Warning: Failed to replace the .gitignore in ' . storage_path(). '/cache/phealcache/.gitignore. You should try this manually.');
            }

            // Attempt to clear the Redis Cache
            try {

                $redis = new \Predis\Client(array('host' => \Config::get('database.redis.default.host'), 'port' => \Config::get('database.redis.default.port')));
                $redis->flushall();
                $this->info('Redis cache cleared.');

            } catch (\Exception $e) {

                $this->error('Warning: Failed to clear the Redis Cache. The error was: ' . $e->getMessage());
            }

            // Lastly, clean up the cached_until timers from the database
            // table.
            try {

                \DB::table('cached_until')->truncate();
                $this->info('DB cache cleared.');

            } catch (\Exception $e) {

                $this->error('Warning: Failed to clear the database cached_until table. The error was: ' . $e->getMessage());

            }
        }
    }
}
