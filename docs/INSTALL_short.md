## SeAT - Simple (or Stupid) EVE Online API Tool ##

### Introduction ###
SeAT is a EVE Online™ Corporation / API Key Management Tool written in PHP using the [Laravel][1] Framework. This guide attempts to detail the process in order to successfully get a new installation setup, configured and ready to use. Lets dive right in.

### Requirements ###
Installing SeAT *should* be relatively simple. At a high level, the main requirements for SeAT is:

 - [PHP (5.3+)][2]
 - [Apache][3]
 - [Redis][4]
 - [supervisord][5]
 - [MySQL][6]

SeAT was developed for Linux and has been tested to work fine on CentOS/Ubuntu. Chances are you could get it going pretty easily on many other Linux flavors, so feel free to contribute docs with install guides. As far as windows support goes... well....

### Installation ###
The quick and dirty to install SeAT:

- Create a MySQL Database, i.e. `seat` with a user for SeAT to use. 
- Install a web server such as Apache together with PHP & MySQL support
- Install Redis
- Clone the SeAT repository into a webroot such as `/var/www/seat`
- Get [composer](https://getcomposer.org/) and run `php composer.phar install` from the project root to install its dependencies.
- Copy `app/config/env-sample.php` to `.env.php` (same folder as `composer.json`) and edit accordingly.  
- Run the SeAT installer with `php artisan seat:install`  
- Install `supervisord` and edit the config to start workers to process queued jobs. A sample worker config is included in `docs/`. See note below
- Setup the SeAT cronjob: `* * * * * php /var/www/seat/artisan scheduled:run 1>> /dev/null 2>&1`
- Optionally setup logrotate for the `app/storage/logs/*.log` files.

Note:  
With supervisord 2 you need to copy the sample and rename to seat2, seat3 etc to start multipe workers. supervisord 3 I beleive introduces a `numprocs` option, that you can just specify to start multiple workers.

  [1]: http://laravel.com/
  [2]: http://www.php.net/
  [3]: http://httpd.apache.org/
  [4]: http://redis.io/
  [5]: http://supervisord.org/
  [6]: http://www.mysql.com/