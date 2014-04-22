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
The following installation assumes you are using Ubuntu. This installation was done on Ubuntu 14.04 however I beleive a Debian install will be pretty much the same no?
The very first step should be to ensure that you are up to date. Run `apt-get update`

### Tutorial! ###
A tutorial showing the full installation is available here: https://asciinema.org/a/9065

#### 1. Install & Configure MySQL ####
As `root`, run:  
    - `apt-get install mysql-server`. When prompted, type a secure root password  
    - `/etc/init.d/mysqld start`  
    - `mysql_secure_installation`  

Next, we will add a user for SeAT and configure a password for it.
As any user, run:  
    - `mysql -uroot -p`. Enter the `root` MySQL password you chose in the previous step.  
    - On the `mysql>` prompt: `create database seat;`  
    - `grant all on seat.* to seat@localhost identified by 'securepassword';`  
    - Press `crtl`+`d` to log out of the prompt.  

#### 2. Install PHP / Apache ####
PHP & MySQL is pretty easy too:  
    - `apt-get install apache2 php5 php5-cli php5-mcrypt php5-mysql php5-curl`  
    - Enable the php mcrypt and modrewrite modules by running `php5enmod mcrypt && a2enmod rewrite`
    - `/etc/init.d/apache2 restart`  
    
#### 3. Install Redis ####
Same thing for Redis  
    - `apt-get install redis-server`  
    
#### 4. Get SeAT ####
Finally, we can get to the SeAT stuff itself. You are going to want to clone the repository. As this is a Git repository, make sure you have `git` installed.  
    - `apt-get install git`  

Next, clone the respoitory somewhere on disk. I'll reccomend `/var/www`:  
    - `cd /var/www`  
    - `git clone https://github.com/eve-seat/seat.git`  
    - Checkout the latest SeAT version found [here](https://github.com/eve-seat/seat/tags) with `git checkout tags/v0.5` (v0.5 is current latest)  
    - Ensure that your webserver owns all of the content with: `chown -R www-data:www-data /var/www/seat`
    - `sudo chmod -R guo+w app/storage` to ensure that the web server can write to the storage location  

After seat is downloaded, we need to get composer to help us install the applications dependencies and generate autoload files:  
    - `cd /var/www/seat`  
    - `curl -sS https://getcomposer.org/installer | php`  
    - `php composer.phar install`  

#### 5. Get EVE SDE's ####
Again, assuming you used `/var/www`, a tool to download the required static data exports can be found in `/var/www/seat/evesde/update_sde.sh`.
This tool can be run with:  
    - `cd /var/www/seat/evesde`  
    - `sh update_sde.sh` (the script will probably error with 'update_sde.sh: 34: update_sde.sh: +: not found. Don't worry, progress reporting us just broken the rest is fine :<)  

Once it completes the downloads, a directory with a name starting with `SeAT-` will be located in `/tmp` on your machine. This directory contains static data that can just be imported into your database. The command will look something like:

`cat /var/SeAT-109831-39871098278970987/*.sql | mysql -u seat -p seat`

Once the import has been completed, you can safely delete the `/tmp/SeAT-*` directory.    

#### 6. Configure SeAT ####
SeAT configuration lives in `app/config`. Assuming you cloned to `/var/www`, the configs will be in `/var/www/seat/app/config`.  

Edit the fowlling files:  
    - database.php (set your configured username/password)  
    - app.php  
    
#### 7. Run the SeAT database migrations & seeds ####
SeAT has all of the database schemas stored in 'migration' scripts in `app/database/migrations`. Run them with:

```bash
$ php artisan migrate
Migrated: 2014_04_20_121149_add_augmentationinfo
```

You should see a relatively large list appear as all the migrations run.

We also need to seed the database with some 'static' data, such as the default `admin` user etc. Run them with

```bash
$ php artisan db:seed
Seeded: UserTableSeeder
Seeded: EveApiCalllistTableSeeder
Seeded: EveNotificationTypesSeeder
```
    
####  8. Install & Configure supervisord ####
Supervisor will be used to keep the workers alive that need to process API jobs. The amount of workers that you start I guess depends on how many keys you have to process. For me, 4 workers seem to work just fine. Set it up by running:  
    - `apt-get install supervisor`  

We now have to configure the actual workers. We do this by adding a new configuration file to `/etc/supervisor/conf.d/` called `seat.conf`. A sample configuration for this block is located in `docs/` and can be used as a boilerplate for your install.
For Ubuntu, we can make use of the numprocs value available in supervisor 3, so, our sample configuration will look like this:

```bash
[program:seat]
command=/usr/bin/php /var/www/seat/artisan queue:listen --timeout=3600 --tries 1
process_name = %(program_name)s-80%(process_num)02d
stdout_logfile = /var/log/seat-80%(process_num)02d.log
stdout_logfile_maxbytes=100MB
stdout_logfile_backups=10
numprocs=4
directory=/var/www/seat
stopwaitsecs=600
user=www-data
```
Once this is done, save the file and restart supervisord with `/etc/init.d/supervisor restart`.

You can checkup on the status of the workers with `supervisorctl status`:

```
[root@server ~]# supervisorctl status
seat:seat-8000                   RUNNING    pid 6996, uptime 0:00:07
seat:seat-8001                   RUNNING    pid 6995, uptime 0:00:07
seat:seat-8002                   RUNNING    pid 6998, uptime 0:00:07
seat:seat-8003                   RUNNING    pid 6997, uptime 0:00:07
```

SeAT should now process jobs that enter the job queue.

####  9. Setup cron ####
Setup the cronjob for SeAT. This job should run as a user that has access to SeAT's location:
  - `crontab -e -u www-data`  
  - Paste this line: `* * * * * php <path to artisan> scheduled:run 1>> /dev/null 2>&1`

#### 10. Security #####
The web interface has a default password of `seat!admin` and it is *highly* recommended that you change this using the `seat:reset` artisan command:

```bash
$ php artisan seat:reset
WARNING!!! This will RESET the current Administrator password!

What is the new password to use for the admin user? :
Retype that password please:
The passwords match. Resetting to the new 12 char one.
Password has been changed successfully.
```

You should regenerate the applications security key:

```bash
$ php artisan key:generate
Application key [YWIs6jkLtVRjhuAENP21KxW7FHD7M0LZ] set successfully.
``` 

#### 11. Web Server ####
In order to get the SeAT frontend running, we need to configure Apache to serve our SeAT installs `public` folder.  
The Apache configuration itself will depend on how your server is set up. Generally, virtual hosting is the way to go, and this is what I will be showing here.

If you are not going to use virtual hosting, the easiest to get going will probably to symlink `/var/www/seat/public/` to `/var/www/html/seat`. This should have SeAT available at http://your ip or hostname/seat 


##### The VirtualHost setup #####
Getting the virtual host setup is as simple as creating a new configuration file for it, and modifying it to match your setup. Everywhere you see `your.domain`, it needs to be substituted to your actual domain:

First we will prepare SeAT. We create the directory `/var/www/your.domain`. Next we symlink the SeAT public directory here `ln -s /var/www/seat/public /var/www/your.domain/seat`.

With that done, we continue to configure Apache for our VirtualHost:  

   - `cd /etc/apache2/sites-available/`
   - Create a file `001-seat.conf`
   - Edit the file and add the following contents:

```bash
<VirtualHost *:80>
    ServerAdmin webmaster@your.domain
    DocumentRoot "/var/www/your.domain"
    ServerName your.domain
    ServerAlias www.your.domain
  ErrorLog ${APACHE_LOG_DIR}/your.domain-error.log
  CustomLog ${APACHE_LOG_DIR}/your.domain-access.log combined
    <Directory "/var/www/your.domain">
        AllowOverride All
        Order allow,deny
        Allow from all
    </Directory>
</VirtualHost>
```

Lastly, we enable this site by symlinking it to `sites-enabled`:  

   - `cd /etc/apache2/sites-enabled`  
   - `ln -s ../sites-available/001-seat.conf 001-seat.conf`  

Lastly, restart apache `/etc/init.d/apache2 restart`

SeAT *should* now be available at http://your.domain/seat

  [1]: http://laravel.com/
  [2]: http://www.php.net/
  [3]: http://httpd.apache.org/
  [4]: http://redis.io/
  [5]: http://supervisord.org/
  [6]: http://www.mysql.com/
