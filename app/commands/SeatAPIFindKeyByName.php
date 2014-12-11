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

class SeatAPIFindKeyByName extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'seat:find-key-by-name';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Search for a API key attached to a character name.';

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

        // Get the name arg from the commandline
        $name = $this->argument('name');
        $this->info('Searching for term: ' . $name);

        // Search for keys with the character on it
        $characters = \EveAccountAPIKeyInfoCharacters::where('characterName', 'like', '%' . $name . '%')->get();

        // If there were results from the find, print
        // them
        if (count($characters) > 0)

            // Loop over the found characeters
            foreach ($characters as $character)
                $this->info('Found match: ' . $character->characterName . ' with keyID: ' . $character->keyID);

        else
            $this->error('No matches found.');
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {

        return array(

            array('name', InputArgument::REQUIRED, 'The name of the character to search the key for.'),
        );
    }
}
