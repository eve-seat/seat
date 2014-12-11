## SeAT - Simple (or Stupid) EVE Online API Tool ##

### Introduction ###
SeAT attempts to be a EVE Online™ Corporation Management Tool written in PHP using the [Laravel][1] Framework. If the fact that its written in PHP makes you mad or want to complain by telling us how bad PHP is (cause you read in online where & someone told you its bad), then I'm pretty happy that we have come this far. If not, well, read on :)

### Requirements ###
Installing SeAT *should* be relatively simple. At a high level, the main requirements for SeAT is:

 - [PHP (5.3+)][2]
 - [Apache][3]
 - [Redis][4]
 - [supervisord][5]
 - [MySQL][6]

SeAT was developed for Linux and has been tested to work fine on CentOS. Chances are you could get it going pretty easily on many other Linux distros, so feel free to contribute docs with install guides. As far as windows support goes... well....

### Installation ###
The quick and dirty to install SeAT:

- Create a MySQL Database, i.e. `seat` with a user for SeAT to use. 
- Install a web server such as Apache together with PHP & MySQL support
- Install Redis
- Clone the SeAT repository into a webroot such as `/var/www/seat`
- Get [composer](https://getcomposer.org/) and run `composer install` from the project root to install its dependencies.
- Copy `app/config/env-sample.php` to `.env.php` (same folder as `composer.json`) and edit accordingly.  
- Run the database migration and seeding scripts with `php artisan migrate` & `php artisan db:seed`
- Install `supervisord` and edit the config to start workers to process queued jobs. A sample worker config is included in `docs/`. See note below
- Setup the SeAT cronjob: `* * * * * php <path to artisan> scheduled:run 1>> /dev/null 2>&1`
- Regenerate the application security key with `php artisan key:generate`
- Create the `admin` user with `php artisan seat:reset`
- Optionally setup logrotate for the `app/storage/logs/*.log` files.

Note:  
With supervisord 2 you need to copy the sample and rename to seat2, seat3 etc to start multipe workers. supervisord 3 I beleive introduces a `numprocs` option, that you can just specify to start multiple workers.

  [1]: http://laravel.com/
  [2]: http://www.php.net/
  [3]: http://httpd.apache.org/
  [4]: http://redis.io/
  [5]: http://supervisord.org/
  [6]: http://www.mysql.com/