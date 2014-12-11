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

class SeatInstall extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'seat:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Installs SeAT.';

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
     * Write configiguration to base_path()/.env.php
     *
     * @return bool
     */
    public function writeConfig($configuration)
    {

        // Prepare the file based off the values in the
        // $configuration array
        $config_file = '<?php // SeAT Configuration Created: ' . date('Y-m-d H:i:s') . "\n\n" .
            'return ' . var_export($configuration, true) . ";\n";

        // Write the configuration file to disk
        $file_write = \File::put(base_path() . '/.env.php', $config_file);

        // Ensure the write was successful
        if ($file_write === false ) {

            $this->error('[!] Writing the configuration file to ' . base_path() . '/.env.php failed!');
            return false;
        }

        return true;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {

        // Certain stages of this installer will require variables
        // to be opulated, which will eventually be used to
        // write the resultant configuration file. Some
        // stages require testing too, so lets
        // prepare some defaults that will
        // be used.
        $configuration = array(
            'mysql_hostname'    => '127.0.0.1',
            'mysql_database'    => 'seat',
            'mysql_username'    => 'root',
            'mysql_password'    => '',
            'redis_host'        => '127.0.0.1',
            'redis_port'        => 6379,
            'mail_driver'       => 'mail',
            'mail_from'         => 'seatadmin@localhost',
            'mail_from_name'    => 'SeAT Administrator',
            'smtp_hostname'     => '127.0.0.1',
            'smtp_username'     => null,
            'smtp_password'     => null,
        );

        $this->info('[+] Welcome to the SeAT v' . \Config::get('seat.version') . ' installer!');
        $this->line('');

        // The very first thing we will be checking is the existence of
        // the .env.php file which contains the configuration of a
        // installed system. If this file exists, we will assume
        // that SeAT is already installed and exit.
        if (\File::exists(base_path() . '/.installed.lck')) {

            $this->error('[!] It appears as if SeAT is already installed. Exiting.');
            return;
        }

        // Next, we will check that we will eventually be able to write
        // to the .env.php in base_path() /.env.php.
        if (!\File::isWritable(base_path() . '/.env.php')) {

            $this->error('[!] The installer needs to be able to write a configuration file to ' . base_path() . '/.env.php, but it appears as though it can not write there.');
            return;
        }

        // Knowing that we can write the configuration file, we move on to
        // getting the details for the database. We will try with the
        // defaults, and if that fails, continue to ask for details
        // until we can connect
        $this->info('[+] Database setup...');
        $this->info('[+] Please enter the details for the MySQL database to use (enter to use default):');
        while (true) {

            // Ask for the MySQL credentials.
            $configuration['mysql_username'] = $this->ask('[?] Username (' . $configuration['mysql_username'] .'):') ? : $configuration['mysql_username'];
            $configuration['mysql_password'] = $this->secret('[?] Password:') ? : $configuration['mysql_password'];
            $configuration['mysql_hostname'] = $this->ask('[?] Hostname (' . $configuration['mysql_hostname'] .'):') ? : $configuration['mysql_hostname'];
            $configuration['mysql_database'] = $this->ask('[?] Database (' . $configuration['mysql_database'] .'):') ? : $configuration['mysql_database'];

            // Set the runtime configuration that we have
            \Config::set('database.connections.mysql.host', $configuration['mysql_hostname']);
            \Config::set('database.connections.mysql.database', $configuration['mysql_database']);
            \Config::set('database.connections.mysql.username', $configuration['mysql_username']);
            \Config::set('database.connections.mysql.password', $configuration['mysql_password']);

            // Test the database connection
            try {

                \DB::connection()->getDatabaseName();
                $this->info('[+] Successfully connected to the MySQL database.');
                $this->line('');

                // If the connection worked, we don't have to ask for anything
                // and just move on to the next section
                break;

            } catch (\Exception $e) {

                $this->error('[!] Unable to connect to the database with mysql://' . $configuration['mysql_username'] . '@' . $configuration['mysql_hostname']);
                $this->error('[!] Please re-enter the configuration to try again.');
                $this->error('[!] MySQL said: ' .$e->getMessage());
                $this->line('');
                $this->info('[+] Please re-enter the MySQL details below:');
            }
        }

        // Now that we have a working database connection, move
        // on to the Redis configuration. We will follow a
        // similar path of a infinite loop until it works
        $this->info('[+] Redis cache setup...');
        $this->info('[+] Please enter the details for the Redis cache to use (enter to use default):');
        while (true) {

            // Ask for the Redis details.
            $configuration['redis_host'] = $this->ask('[?] Host (' . $configuration['redis_host'] .'):') ? : $configuration['redis_host'];
            $configuration['redis_port'] = $this->ask('[?] Port (' . $configuration['redis_port'] .'):') ? : $configuration['redis_port'];

            // Set the \Config for this one runtime to test the connection
            \Config::set('database.redis.default.host', $configuration['redis_host']);
            \Config::set('database.redis.default.port', $configuration['redis_port']);

            // Test that we can add and remove keys from the cache
            try {

                \Cache::put('installer_test', true, 60 * 24);
                \Cache::forget('installer_test');
                $this->info('[+] Successfully connected to the Redis cache.');
                $this->line('');

                // If the connection worked and we were able to place
                // and delete a key, move on to the next section
                break;

            } catch (\Exception $e) {

                $this->error('[!] Unable to connect to the redis cache at tcp://' . $configuration['redis_host'] . ':' . $configuration['redis_port']);
                $this->error('[!] Please re-enter the configuration to try again.');
                $this->error('[!] Redis said: ' .$e->getMessage());
                $this->line('');
                $this->info('[+] Please re-enter the Redis details below:');
            }
        }

        // We now have MySQL + Redis setup and ready. Lets move on the
        // the email configurations. If we have mail/sendmail as
        // the config, its easy. However, if we use SMTP, we
        // need to ask the user for credentials too, incase
        // those are needed. We will also start another
        // infinite loop to allow the user to confirm
        // that the details they entered is correct.
        $this->info('[+] Mail setup...');
        $this->info('[+] Please enter the details for the email configuration to use (enter to use default):');
        while (true) {

            // Ask for the email details
            $configuration['mail_driver'] = $this->ask('[?] How are emails going to be sent? [mail/sendmail/smtp] (' . $configuration['mail_driver'] .'):') ? : $configuration['mail_driver'];

            // Check the option we got. If it is not in the array of
            // known configuration, we return to the question
            if (!in_array($configuration['mail_driver'],  array('mail', 'sendmail', 'smtp'))) {

                $this->error('[!] The driver you have chosen is not recognized, please try again.');
                continue;

            }

            // Get the details about where emails will be coming from
            $configuration['mail_from'] = $this->ask('[?] Where will emails be coming from? (' . $configuration['mail_from'] .'):') ? : $configuration['mail_from'];
            $configuration['mail_from_name'] = $this->ask('[?] Who will emails be coming from? (' . $configuration['mail_from_name'] .'):') ? : $configuration['mail_from_name'];

            // If the configuration option is set as smtp, we need to
            // give the option to set the username and password
            if ($configuration['mail_driver'] == 'smtp') {

                $configuration['smtp_hostname'] = $this->ask('[?] SMTP Hostname (' . $configuration['smtp_hostname'] .'):') ? : $configuration['smtp_hostname'];
                $configuration['smtp_username'] = $this->ask('[?] SMTP Username (' . $configuration['smtp_username'] .'):') ? : $configuration['smtp_username'];
                $configuration['smtp_password'] = $this->secret('[?] SMTP Password:') ? : $configuration['smtp_password'];
            }

            // Print the values and get confirmation that they are correct
            $this->line('');
            $this->line('[+] Mail configuration summary:');
            $this->line('[+]    Mail Driver: ' . $configuration['mail_driver']);

            // If we are going to be using the SMTP driver, show the
            // values for the host/user/pass
            if ($configuration['mail_driver'] == 'smtp') {

                $this->line('[+]    SMTP Host: ' . $configuration['smtp_hostname']);
                $this->line('[+]    SMTP Username: ' . $configuration['smtp_username']);
                $this->line('[+]    SMTP Password: ' . str_repeat('*', strlen($configuration['smtp_password'])));
            }
            $this->line('');


            if ($this->confirm('[?] Are the above mail settings correct? [yes/no]', true))
                break;
            else
                continue;
        }

        // With the configuration done, lets attempt to write this to
        // to disk
        if (!$this->writeConfig($configuration)) {

            $this->error('[!] Writing the configuration file failed!');
            return;
        }

        $this->info('[+] Successfully wrote the configuration file');

        // With configuration in place, lets move on to preparing SeAT
        // for use. We have to do a few things for which most
        // already have commands. So, lets re-use those
        // meaning that if they change the intaller
        // is already up to date.

        // Run the database migrations
        $this->info('[+] Running the database migrations...');
        $this->call('migrate');

        // Run the database seeds
        $this->info('[+] Running the database seeds...');
        $this->call('db:seed');

        // Update the SDEs
        $this->info('[+] Updating to the latest EVE SDE\'s...');
        $this->call('seat:update-sde', array('--confirm' => null));

        // Configure the admin user
        $this->info('[+] Configuring the \'admin\' user...');
        $this->call('seat:reset');

        // Sync the access groups
        $this->info('[+] Syncing the access groups...');
        $this->call('seat:groupsync');
        $this->line('');

        // Finally, write the installer lock file!
        $lock_file_write = \File::put(base_path() . '/.installed.lck', 'Installed ' . date('Y-m-d H:i:s'));

        // Check that we wrote the lock file successfully
        if (!$lock_file_write)
            $this->error('[!] Was not able to write the installation lock file! Please touch \'installed.lck\'.');

        $this->info('[+] Done!');
    }
}
