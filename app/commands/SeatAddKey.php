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

use Pheal\Pheal;
use Seat\EveApi;
use Seat\EveApi\BaseApi;
use Seat\EveApi\Account;

class SeatAddKey extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'seat:add-key';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Adds a API Key to SeAT.';

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
        
        $admin = \User::where('username', '=', 'admin');

        if(!$admin) {
            
            $this->error('Admin account doesn\'t exist. Run seat:reset to create it.');
            return;
        }

        // Get the KeyID and vCode from the command args
        $keyID = $this->argument('keyID');
        $vCode = $this->argument('vCode');

        // Next, we validate the keypair we got to ensure
        // that the format is correct.
        EveApi\BaseApi::validateKeyPair($keyID, $vCode);

        $this->info('The key pair appear to be valid.');
        $this->line('Checking if key exists in the database...');

        // Check if the key is known in the database
        if (!\SeatKey::where('keyID', $keyID)->first()) {

            $this->info('keyID is not present in the database. Making the APIKeyInfo API call...');

            // Boot up a pheal instance and get some API data
            BaseApi::bootstrap();
            $pheal = new Pheal($keyID, $vCode);

            // Get API Key Information
            try {

                $key_info = $pheal->accountScope->APIKeyInfo();

            } catch (\Pheal\Exceptions\PhealException $e) {

                $this->error('Unable to retreive key information. The erorr was: ' . $e->getCode() . ': ' . $e->getMessage());
                return;
            }

            // Print some information about the key
            $this->line('Key Type: ' . $key_info->key->type);
            $this->line('Key Access Mask: ' . $key_info->key->accessMask);
            $this->info('Characters on key: ');

            // Loop over the characters on the key and print
            // them too
            foreach ($key_info->key->characters as $character)
                $this->line('   - ' . $character->characterName . ' in corporation ' . $character->corporationName);

            // Confirm adding the key
            if ($this->confirm('Do you wish to add this key to the database? [yes|no]')) {

                $key_info = new \SeatKey;

                $key_info->keyID = $keyID;
                $key_info->vCode = $vCode;
                $key_info->isOk = 1;
                $key_info->lastError = null;
                $key_info->deleted_at = null;
                $key_info->user_id = $admin->getKey();
                $key_info->save();

                $this->info('Successfully saved the API key to the database.');
            }

        } else {

            $this->error('keyID ' . $keyID . ' already exists in the database.');
        }

    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {

        return array(

            array('keyID', InputArgument::REQUIRED, 'The keyID.'),
            array('vCode', InputArgument::REQUIRED, 'The vCode.'),
        );
    }

}
