## SeAT - Simple (or Stupid) EVE Online API Tool ##

### Introduction ###
SeAT attempts to be a EVE Online™ Corporation Management Tool written in PHP using the [Laravel][1] Framework. If the fact that its written in PHP makes you mad or want to complain by telling us how bad PHP is (cause you read it online where someone told you its bad), then I'm pretty happy that we have come this far. If not, well, read on :)

### Requirements ###
Installing SeAT *should* be relatively simple. At a high level, the main requirements for SeAT is:

 - [PHP (5.3+)][2]
 - [Apache][3]
 - [Redis][4]
 - [supervisord][5]
 - [MySQL][6]

SeAT was developed for Linux and has been tested to work fine on CentOS. Chances are you could get it going pretty easily on many other Linux distros, so feel free to contribute docs with install guides. As far as windows support goes... well....

### Installation ###
The following installation assumes you are using CentOS. For the Debian/Ubuntu folk, most of the `yum install` commands will probably end up being things like `apt-get install`. A seperate guide for Debian / Ubuntu can be found in `docs/`. 

For CentOS, the EPEL repository is needed for Redis and supervisod, so download and install it with the following 2 commands:

```bash
$ EPEL=epel-release-6-8.noarch.rpm && BASEARCH=$(uname -i)
$ wget http://dl.fedoraproject.org/pub/epel/6/$BASEARCH/$EPEL -O $EPEL && yum localinstall -y $EPEL && rm -f $EPEL
```

*note* If you dont have the `wget` command, just install it with `yum install wget -y`


### Tutorial! ###
A tutorial showing the full installation is available here: https://asciinema.org/a/9057  
**NOTE:** The above tutorial does not include the SELinux stuff. **Don't** turn it off! Simply refer to section 10 below for the configuration steps.

#### 1. Install & Configure MySQL ####
For CentOS, installation is pretty simple:
As `root`, run:  
    - `yum install mysql-server`  
    - `chkconfig mysqld on`  
    - `/etc/init.d/mysqld start`  
    - `mysql_secure_installation`, choosing a strong password for your `root` mysql user  

Next, we will add a user for SeAT and configure a password for it.
As any user, run:  
    - `mysql -uroot -p`. Enter the `root` MySQL password you chose in the previous step.  
    - On the `mysql>` prompt: `create database seat;`  
    - `grant all on seat.* to seat@localhost identified by 'securepassword';`  
    - Press `crtl`+`d` to log out of the prompt.  

#### 2. Install & Configure PHP / Apache ####
PHP & MySQL is pretty easy too:  
    - `yum install httpd php php-mysql php-cli php-mcrypt php-process php-mbstring`  
    - `chkconfig httpd on`  
    - `/etc/init.d/httpd start`  
    
#### 3. Install & Configure Redis ####
Same thing for Redis  
    - `yum install redis`  
    - `chkconfig redis on`  
    - `/etc/init.d/redis start`  
    
#### 4. Get SeAT ####
Finally, we can get to the SeAT stuff itself. You are going to want to clone the repository. As this is a Git repository, make sure you have `git` installed.  
    - `yum install git`  

Next, clone the respoitory somwhere on disk. I'll reccomend `/var/www`:  
    - `cd /var/www`  
    - `git clone https://github.com/eve-seat/seat.git`  
    - Move to the new `seat` directory with `cd seat/`  
    - Checkout the latest SeAT version found [here](https://github.com/eve-seat/seat/tags) with `git checkout -b 0.8 tags/v0.8` (v0.8 is current latest but may have changed. Hint: [![Latest Release](http://img.shields.io/github/release/eve-seat/seat.svg)](https://github.com/eve-seat/seat/releases/latest))  
    - Ensure that your webserver owns all of the content with:  
      - `sudo chown -R apache:apache /var/www/seat`  
      - `sudo chmod -R guo+w app/storage` to ensure that the web server can write to the storage location  

After seat is downloaded, we need to get composer to help us install the applications dependencies and generate autoload files:  
    - `cd /var/www/seat`  
    - `curl -sS https://getcomposer.org/installer | php`  
    - `php composer.phar install`  

#### 5. Get EVE SDE's ####
Again, assuming you used `/var/www` as the location to install SeAT, a tool to download the required static data exports can be found in `/var/www/seat/evesde/update_sde.sh`.
This tool can be run with:  
    - `cd /var/www/seat/evesde`  
    - `sh update_sde.sh`  

The downloader can automatically import the required SDE's into MySQL too. A sample run with the automatic updates may look like:

```bash
[root@seat evesde]# sh update_sde.sh
Automatically import required SQL files? [y/N] y
Enter database user: seat
Enter database password:
Enter database server (empty for localhost):
Enter database name: seat
Creating temp directory /tmp/SeAT-3b722357aa3e44e16869877ca3924665...
Getting dump dgmTypeAttributes from https://www.fuzzwork.co.uk/dump/rubicon-1.3-95173/dgmTypeAttributes.sql.bz2...
Extracting /tmp/SeAT-3b722357aa3e44e16869877ca3924665/dgmTypeAttributes.sql.bz2 ...
<snip>
Importing the SQL files into MySQL...
[root@seat evesde]#
```

If you chose not to automatically import the SQL, a directory with a name starting with `SeAT-` will be located in `/tmp` on your machine. This directory containts the static data that can just be imported into your database. The command will look something like:

`cat /var/SeAT-109831-39871098278970987/*.sql | mysql -u seat -p seat`

Once the import has been completed, you can safely delete the `/tmp/SeAT-*` directory.    

#### 6. Configure SeAT ####
SeAT configuration lives in `app/config`. Assuming you cloned to `/var/www`, the configs will be in `/var/www/seat/app/config`.  

Edit the fowlling files:  
    - `database.php` (set your configured username/password)  
    - `cache.php`  (may not require any changes, depending on how you wish to setup SeAT)
    
#### 7. Run the SeAT database migrations & seeds ####
SeAT has all of the database schemas stored in 'migration' scripts in `app/database/migrations`. Run them with:

```bash
$ cd /var/www/seat
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
Supervisor will be used to keep the workers alive that need to process API jobs. The amount of workers that you start I guess depends on how many keys you have to process. For me, 4 workers seem to work just fine processing ~150 keys. Set it up by running:  
    - `yum install supervisor`  
    - `chkconfig supervisord on`  

We now have to configure the actual workers. We go this by adding `program` configuration blocks to `/etc/supervisord.conf`. A sample configuration for this block is located in `docs/` and can simply be added to the bottom of the existing configuration file. Note that the sample has the program defined as `[program:seat1]`. If you want to run 4 workers, you need to add this to the `supervisord.conf` *4* times. So you will have 4 blocks with incrementing numbers ie. `[program:seat1]`, `[program:seat2]`, `[program:seat3]` & `[program:seat4]`.

Once this is done, save the file and start supervisord with `/etc/init.d/supervisord start`.

You can checkup on the status of the workers with `supervisorctl status`:

```
[root@server ~]# supervisorctl status
seat1          RUNNING    pid 23583, uptime 1 day, 0:18:27
seat2          RUNNING    pid 23584, uptime 1 day, 0:18:27
seat3          RUNNING    pid 23585, uptime 1 day, 0:18:27
seat4          RUNNING    pid 23582, uptime 1 day, 0:18:27
```

SeAT should now process jobs that enter the job queue.

####  9. Setup cron ####
Setup the cronjob for SeAT. This job should run as a user that has access to SeAT's location:
  - `crontab -e`  
  - Paste this line: `* * * * * /usr/bin/php /var/www/seat/artisan scheduled:run 1>> /dev/null 2>&1`

#### 10. Security #####
The web interface has a default account for the user `admin`, and should have its password set with `php artisan seat:reset`

```bash
$ php artisan seat:reset
WARNING!!! This will RESET the current Administrator password!

What is the new password to use for the admin user? :
Retype that password please:
The passwords match. Resetting to the new 12 char one.
Password has been changed successfully.
```

You should also seed the default SeAT user groups with `php artisan seat:groupsync`:

```bash
$ php artisan seat:groupsync
[info] Group POS Managers was not found. Creating it.
[ok] Group POS Managers created.
[info] Group Wallet Managers was not found. Creating it.
[ok] Group Wallet Managers created.
[info] Group Recruiters was not found. Creating it.
[ok] Group Recruiters created.
[info] Group Asset Managers was not found. Creating it.
[ok] Group Asset Managers created.
[info] Group Contract Managers was not found. Creating it.
[ok] Group Contract Managers created.
[info] Group Market Managers was not found. Creating it.
[ok] Group Market Managers created.
```


You should regenerate the applications security key:

```bash
$ php artisan key:generate
Application key [YWIs6jkLtVRjhuAENP21KxW7FHD7M0LZ] set successfully.
``` 

**SELinux**  
This is a part that many people struggle with and as a result, they simply turn selinux off. This is bad, don't do it. SELinux can be configured as needed. To configure it to work with SeAT, you need to do 2 things.

1) Ensure that your web content in `/var/www/seat` is laballed correctly. To do this, run (as root):
```bash
$ restorecon -Rv /var/www/seat
```

2) Next, we need to allow the apache process to make network connections. Allow this with (as root again):  
```bash
setsebool -P httpd_can_network_connect 1
```

Done! :)


#### 11. Web Server ####
In order to get the SeAT frontend running, we need to configure Apache to serve our SeAT installs `public` folder.  
The Apache configuration itself will depend on how your server is set up. Generally, virtual hosting is the way to go, and this is what I will be showing here.

If you are not going to use virtual hosting, the easiest to get going will probably to symlink `/var/www/seat/public/` to `/var/www/html/seat` and configuring apache to `AllowOverride All` in the `<Directory "/var/www/html">` section. This should have SeAT available at http://your ip or hostname/seat after you restart apache.


##### The VirtualHost setup #####
Getting the virtual host setup is as simple as creating a new configuration file (i usually call it the domain.conf), and modifying it to match your setup. Everywhere you see `your.domain`, it needs to be substituted to your actual domain:

First we will prepare SeAT. We create the directory `/var/www/html/your.domain`. Next we symlink the SeAT public directory here `ln -s /var/www/seat/public /var/www/html/your.domain/seat`.

With that done, we continue to configure Apache for our VirtualHost:  

   - `cd /etc/httpd/conf.d`
   - Create a file `your.domain.conf`
   - Edit the file and add the following contents:

```bash
<VirtualHost *:80>
    ServerAdmin webmaster@your.domain
    DocumentRoot "/var/www/html/your.domain"
    ServerName your.domain
    ServerAlias www.your.domain
    ErrorLog "logs/your.domain-error_log"
    CustomLog "logs/your.domain-access_log" common
    <Directory "/var/www/html/your.domain">
        AllowOverride All
        Order allow,deny
        Allow from all
    </Directory>
</VirtualHost>
```

   - Softrestart Apache: `apachectl graceful`

SeAT *should* now be available at http://your.domain/seat

  [1]: http://laravel.com/
  [2]: http://www.php.net/
  [3]: http://httpd.apache.org/
  [4]: http://redis.io/
  [5]: http://supervisord.org/
  [6]: http://www.mysql.com/
