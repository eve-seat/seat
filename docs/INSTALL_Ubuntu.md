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

### Video Tutorial ###
A visual walkthrough of installing SeAT on Ubuntu is hosted on asciicinema and can be viewed [here](https://asciinema.org/a/14884). The walkthrough follows the exact same steps as detailed in the below guide, so feel free to follow along. It also shows that you can install SeAT in 13 minutes :D

### Installation Preparation ###
The following guide assumes you are using Ubuntu and are currently logged in as the `root` user. A separate guide for CentOS can be found in `docs/`.  

#### 1. Install & Configure MySQL ####
We will prepare a MySQL server instance for SeAT to use. If you already have a database server that you want to use, you may skip the installation section and move on to creating the database and user for SeAT.

##### MySQL Server #####
So, to install the database server, we run `apt-get install mysql-server`:

```bash
root@seat:~# apt-get install mysql-server
Reading package lists... Done
Building dependency tree
Reading state information... Done
The following extra packages will be installed:
  libaio1 libdbd-mysql-perl libdbi-perl libhtml-template-perl libmysqlclient18
  libterm-readkey-perl mysql-client-5.5 mysql-client-core-5.5 mysql-common
  mysql-server-5.5 mysql-server-core-5.5
[... snip ...]
Setting up mysql-server (5.5.40-0ubuntu0.14.04.1) ...
Processing triggers for libc-bin (2.19-0ubuntu6) ...
```

Next, we start the database server with `/etc/init.d/mysql start`:

```bash
root@seat:~# /etc/init.d/mysql start
 * Starting MySQL database server mysqld           [ OK ]
```

Lastly, we have to ensure that the database server is secure and that no unauthorized access is possible. MySQL comes shipped with a helper command for that, so we will invoke ith with `mysql_secure_installation`. Make sure you choose a strong MySQL `root` password and remember it:

```bash
root@seat:~# mysql_secure_installation

[... snip ...]

Set root password? [Y/n] y
New password:
Re-enter new password:
Password updated successfully!
Reloading privilege tables..
 ... Success!
 
 [... snip ...]

Remove anonymous users? [Y/n] y
 ... Success!

[... snip ...]

Disallow root login remotely? [Y/n] y
 ... Success!

[... snip ...]

Remove test database and access to it? [Y/n] y
 - Dropping test database...
 ... Success!
 - Removing privileges on test database...
 ... Success!

[... snip ...]

Reload privilege tables now? [Y/n] y
 ... Success!
 
 [... snip ...]
```

And our MySQL server installation is done.

##### MySQL Database and User #####
With a database server ready to use, we will move on to configuring the actual database SeAT will use, along with a user that will access this database.

First, we have to connect to the database server as the `root` MySQL user with `mysql -uroot -p`. The `-p` will tell the MySQL client to prompt you for the MySQL `root` password:


```bash
root@seat:~# mysql -uroot -p
Enter password:
Welcome to the MySQL monitor.  Commands end with ; or \g.
Your MySQL connection id is 11
Server version: 5.1.73 Source distribution

Copyright (c) 2000, 2013, Oracle and/or its affiliates. All rights reserved.

Oracle is a registered trademark of Oracle Corporation and/or its
affiliates. Other names may be trademarks of their respective
owners.

Type 'help;' or '\h' for help. Type '\c' to clear the current input statement.

mysql>
```

Next, we create a database for SeAT to use with `create database seat;`:

```bash
mysql> create database seat;
Query OK, 1 row affected (0.00 sec)
```

Lastly, we add a MySQL user for SeAT with the `GRANT` statement:

```bash
mysql> GRANT ALL ON seat.* to seat@localhost IDENTIFIED BY 's_p3rs3c3r3tp455w0rd';
Query OK, 0 rows affected (0.00 sec)
mysql> \q
Bye
[root@seat ~]#
```

The database should now be ready to use!  
You can optionally test the connection with the `mysql` client:

```bash
[root@seat ~]# mysql -useat -p #enter your s_p3rs3c3r3tp455w0rd at the prompt
Welcome to the MySQL monitor.  Commands end with ; or \g.
Your MySQL connection id is 13
Server version: 5.1.73 Source distribution

Copyright (c) 2000, 2013, Oracle and/or its affiliates. All rights reserved.

Oracle is a registered trademark of Oracle Corporation and/or its
affiliates. Other names may be trademarks of their respective
owners.

Type 'help;' or '\h' for help. Type '\c' to clear the current input statement.

mysql> \q
Bye
[root@seat ~]#
```

**Note:** Remember the password for the `seat` user as you will need to provide it later when actually installing SeAT itself!

#### 2. Install & Configure PHP / Apache ####
Next we will prepare the web server along with PHP. If you plan on only using the SeAT for backend purposes then you can skip the `apache2` package.

So, lets install the dependencies with `apt-get install apache2 php5 php5-cli php5-mcrypt php5-mysql php5-curl`

```bash
root@seat:~# apt-get install apache2 php5 php5-cli php5-mcrypt php5-mysql php5-curl
Reading package lists... Done
Building dependency tree
Reading state information... Done
The following extra packages will be installed:
  apache2-bin apache2-data libapache2-mod-php5 libapr1 libaprutil1
  libaprutil1-dbd-sqlite3 libaprutil1-ldap libmcrypt4 php5-common php5-json
  php5-readline ssl-cert
[... snip ...]
Setting up php5 (5.5.9+dfsg-1ubuntu4.5) ...
Processing triggers for libc-bin (2.19-0ubuntu6) ...
```

Next we have to enable PHP and modrewrite. We can do this with `php5enmod mcrypt && a2enmod rewrite`:

```bash
root@seat:~# php5enmod mcrypt && a2enmod rewrite
Enabling module rewrite.
To activate the new configuration, you need to run:
  service apache2 restart
```

And then lastly, we restart Apache with `/etc/init.d/apache2 restart`:

```bash
root@seat:~# /etc/init.d/apache2 restart
 * Restarting web server apache2                         [ OK ]
```

#### 3. Install & Configure Redis ####
The SeAT Jobs system as well as caching occurs in Redis. At present this is the only supported backend for Queues and Cache. So just like we have been installing the other dependencies, we will do the same with `apt-get install redis-server`:

```bash
root@seat:~# apt-get install redis-server
Reading package lists... Done
Building dependency tree
Reading state information... Done
The following extra packages will be installed:
  libjemalloc1 redis-tools
The following NEW packages will be installed:
  libjemalloc1 redis-server redis-tools
[... snip ...]
Starting redis-server: redis-server.
Processing triggers for libc-bin (2.19-0ubuntu6) ...
Processing triggers for ureadahead (0.100.0-16) ..
```

We now have all the required dependencies to install SeAT! :)

#### 4. Get SeAT ####

##### Preparing Git #####
The SeAT source code is hosted on [Github](https://github.com/eve-seat/seat) and to get a copy of it, we will need the `git` client. So, first check that you have `git` installed with `git --version`:

```bash
[root@seat ~]# git --version
git version 1.7.1
```

If you get a error such as `The program 'git' is currently not installed.`, simply install it using `apt-get` with `apt-get install git`:

```bash
root@seat:~# apt-get install git
Reading package lists... Done
Building dependency tree
Reading state information... Done
The following extra packages will be installed:
  git-man liberror-perl
Suggested packages:
  git-daemon-run git-daemon-sysvinit git-doc git-el git-email git-gui gitk
  gitweb git-arch git-bzr git-cvs git-mediawiki git-svn
The following NEW packages will be installed:
  git git-man liberror-perl
[... snip ...]
Setting up git-man (1:1.9.1-1) ...
Setting up git (1:1.9.1-1) ...
```

##### Getting SeAT #####
Now we finally get to the part of downloading SeAT. This is also the time where you need to decide where you want SeAT to live on your server. I am going to use `/var/www` in the tutorial.

So, change directories to `/var/www`:

```bash
root@seat:~# cd /var/www/
root@seat:/var/www#
```

If you check the directory listing with `ls`, you will notice a few folders already present:

```bash
root@seat:/var/www# ls
html
```

And finally, clone the SeAT repository from Github with `git clone https://github.com/eve-seat/seat.git`:

```bash
root@seat:/var/www# git clone https://github.com/eve-seat/seat.git
Cloning into 'seat'...
remote: Counting objects: 6088, done.
remote: Compressing objects: 100% (173/173), done.
remote: Total 6088 (delta 92), reused 0 (delta 0)
Receiving objects: 100% (6088/6088), 2.26 MiB | 352.00 KiB/s, done.
Resolving deltas: 100% (4137/4137), done.
Checking connectivity... done.
```

If you have to view the directory listing again now using `ls`, you should notice the new `seat` directory:

```bash
root@seat:/var/www# ls
html  seat
```

Change to the new SeAT directory using `cd seat` as we have some installation work to do here. You should be in the directory `/var/www/seat` after the `cd` command. This can be checked with the `pwd` command after the `cd`:

```bash
root@seat:/var/www# cd seat/
root@seat:/var/www/seat# pwd
/var/www/seat
```

##### File Permissions #####

SeAT writes logfiles/cachefiles and other temporary data to the `app/storage` directory. That together with the fact that the web content will be hosted by apache means that we need to configure the files permissions to allow SeAT do do its thing.

First, lets ensure that apache owns everything in `/var/www/seat` which is the folder we just downloaded SeAT to with `chown -R www-data:www-data /var/www/seat`:

```bash
root@seat:/var/www/seat# chown -R www-data:www-data /var/www/seat
root@seat:/var/www/seat#
```

Next, we will allow Apache to write to the `app/storage` directory so that it may manipulate the files in there as needed with `chmod -R guo+w /var/www/seat/app/storage`:

```bash
root@seat:/var/www/seat# chmod -R guo+w /var/www/seat/app/storage
root@seat:/var/www/seat#
```

SeAT is now downloaded and almost ready to start being useful!

#### 5. SeAT Configuration File ####
Some of the SeAT settings need to live in a configuration file. Thankfully, this is not one you have to edit manually, but you do need to provide a sample for the SeAT installer to use later. A sample file lives in `app/config/env-sample.php`, and needs to be copied to `.env.php` in your `seat/` folder. So, while you are still in `/var/www/seat`, copy the sample with `cp app/config/env-sample.php .env.php`:

```bash
root@seat:/var/www/seat# pwd
/var/www/seat
root@seat:/var/www/seat# cp app/config/env-sample.php .env.php
root@seat:/var/www/seat#
```

#### 5. The Composer Dependency Manager ####
SeAT relies heavily on [composer](https://getcomposer.org/) to manage its internal dependencies. `composer` is a command line tool that should be downloaded separately. Once downloaded, we will have a file called `composer.phar`, which will be executed to update the SeAT dependencies amongst other things.

So, lets install `composer`. We can store the file in the same folder where our `composer.json` lives, which will therefore be `/var/www/seat` (make sure that is the directory you are in before downloading). Then we download `composer` with `curl -sS https://getcomposer.org/installer | php`:

```bash
root@seat:/var/www/seat# curl -sS https://getcomposer.org/installer | php
#!/usr/bin/env php
All settings correct for using Composer
Downloading...

Composer successfully installed to: /var/www/seat/composer.phar
Use it: php composer.phar
```

With `composer` now ready to use, we start the dependency installation with `php composer.phar install` (`composer.phar` is in the same directory as `composer.json` in the below example). Note that this could take some time to complete:

```bash
root@seat:/var/www/seat# php composer.phar install
Loading composer repositories with package information
Installing dependencies (including require-dev)
  - Installing 3rdpartyeve/phealng (1.3.0)
    Downloading: 100%
    
[... snip ...]

Generating optimized class loader
Compiling common classes
root@seat:/var/www/seat#
```

** NEARLY THERE **

#### 5. The SeAT Installer ####
The next step is to invoke the SeAT installer. The installer is responsible for ensuring that the configuration files are correct, the SeAT install has been configured for email and that the database is ready for use.

**Installer Notes:** The installer the only continue once it has successfully made a connection to a MySQL database and a Redis cache. Please ensure that these have already been configured as per the previous steps.  
**Notes about mail:** The SeAT installer will provide you with 3 options for email. `mail`, which will use PHP's mailer, `smtp`, which will prompt you for optional credentials and `sendmail`.

All settings made by the installer may be changed at a later state as they live in the `.env.php` file.
So, lets invoke the installer with `php artisan seat:install`:

```bash
root@seat:/var/www/seat# php artisan seat:install
[+] Welcome to the SeAT v0.11.0 installer!

[+] Database setup...
[+] Please enter the details for the MySQL database to use (enter to use default):
[?] Username (root):

[... snip ...]
SDE update to rhea-1.0-109013 completed successfully.
[+] Configuring the 'admin' user...
WARNING!!! This will RESET the current Administrator password!

What is the new password to use for the admin user? :
[... snip ...]
[ok] Group Key Manager created.

[+] Done!
```

And thats it :)
If the installer failed, you can safely go and rectify what ever is needed and re-run it.

####  8. Install & Configure supervisord ####
SeAT makes use of *workers* to actually process the update jobs that get scheduled. Think if the architecture as someone coming and dumping mail at the postoffice, and its up to say 4 workers to dig through the mail and sort it. Those 4 workers need a manager to ensure that they keep working. `supervisord` is a excellent candidate for the job.

So lets install supervisord with `apt-get install supervisor`:

```bash
root@seat:/var/www/seat# apt-get install supervisor
Reading package lists... Done
Building dependency tree
Reading state information... Done
The following extra packages will be installed:
  python-meld3
The following NEW packages will be installed:
  python-meld3 supervisor
[... snip ...]
Starting supervisor: supervisord.
Processing triggers for ureadahead (0.100.0-16) ...
```

We now have to configure the actual workers that `supervisord` will manage. We do this by adding a new configuration file to `/etc/supervisor/conf.d/` called `seat.conf` A sample configuration for this file is located in `docs/` and at the end of this paragraph. Note that the number of workers that we want to start is set by the `numprocs` settings.

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

Once this is done, save the file and restart supervisord with `/etc/init.d/supervisor restart`:

```bash
root@seat:/var/www/seat# /etc/init.d/supervisor restart
Restarting supervisor: supervisord.
```

You can checkup on the status of the workers with `supervisorctl status`:

```bash
root@seat:/var/www/seat# supervisorctl status
seat:seat-8000                   RUNNING    pid 8181, uptime 0:00:13
seat:seat-8001                   RUNNING    pid 8180, uptime 0:00:13
seat:seat-8002                   RUNNING    pid 8183, uptime 0:00:13
seat:seat-8003                   RUNNING    pid 8182, uptime 0:00:13
```

SeAT should now process jobs that enter the job queue.

####  9. Setup cron ####
While we now have workers ready, and a supervisor for them, we need to configure the part that is responsible for generating the work. SeAT has a preconfigured schedule at which work will come in, however, we need to check every minute of there is work to do. For that we setup a simple cronjob for the web server user with `crontab -u www-data -e`, adding the following line to it `* * * * * /usr/bin/php /var/www/seat/artisan scheduled:run 1>> /dev/null 2>&1`:

```bash
root@seat:/var/www/seat# crontab -u www-data -e
# paste * * * * * /usr/bin/php /var/www/seat/artisan scheduled:run 1>> /dev/null 2>&1
```

#### 11. Web Server ####
In order to get the SeAT fronted running, we need to configure Apache to serve our SeAT installs `public/` folder.  
The Apache configuration itself will depend on how your server is set up. Generally, virtual hosting is the way to go, and this is what I will be showing here.

If you are not going to use virtual hosting, the easiest to get going will probably to symlink `/var/www/seat/public/` to `/var/www/html/seat` and configuring apache to `AllowOverride All` in the `<Directory "/var/www/html">` section. This should have SeAT available at http://hostname/seat.

##### The VirtualHost setup #####
Getting the virtual host setup is as simple as creating a new configuration file (I usually call it the domain.conf), and modifying it to match your setup. Everywhere you see `seat.local`, it needs to be substituted to your actual domain:

First we will prepare SeAT. We create the directory `/var/www/html/seat.local` with `mkdir /var/www/html/seat.local`:

```bash
[root@seat seat]# mkdir /var/www/html/seat.local
[root@seat seat]#
```

Next we symlink the SeAT public directory there with `ln -s /var/www/seat/public /var/www/html/seat.local/seat`.

```bash
[root@seat seat]# ln -s /var/www/seat/public /var/www/html/seat.local/seat
[root@seat seat]#
```

With that done, we continue to configure Apache for our VirtualHost. First we change to the directory `/etc/apache2/sites-available/` which will have our `001-seat.conf` configuration file with `cd /etc/apache2/sites-available/`:

```bash
root@seat:/var/www/seat# cd /etc/apache2/sites-available/
root@seat:/etc/apache2/sites-available#
```

Next, we create the file `001-seat.conf`, pasting the following contents into it:

```bash
<VirtualHost *:80>
    ServerAdmin webmaster@your.domain
    DocumentRoot "/var/www/html/seat.local"
    ServerName seat.local
    ServerAlias www.seat.local
    ErrorLog ${APACHE_LOG_DIR}/seat.local-error.log
    CustomLog ${APACHE_LOG_DIR}/seat.local-access.log combined
    <Directory "/var/www/html/seat.local">
        AllowOverride All
        Order allow,deny
        Allow from all
    </Directory>
</VirtualHost>
```

With the configuration file ready, we will symling the configuration file to `sites-enabled`, so that it is loaded when apache starts with `ln -s /etc/apache2/sites-available/001-seat.conf /etc/apache2/sites-enabled/001-seat.conf`

```bash
root@seat:/etc/apache2/sites-available# ln -s /etc/apache2/sites-available/001-seat.conf /etc/apache2/sites-enabled/001-seat.conf
```

And finally, we will restart Apache, and check if it sees our new Virtual Host. Restart apache with `apachectl restart`:

```bash
root@seat:/etc/apache2/sites-available# /etc/init.d/apache2 restart
 * Restarting web server apache2                              [ OK ]
```

Lastly, confirm the our virtual host is present with `apachectl -t -D DUMP_VHOSTS`:

```bash
root@seat:/etc/apache2/sites-available# apachectl -t -D DUMP_VHOSTS
VirtualHost configuration:
*:80                   is a NameVirtualHost
         default server seat.local (/etc/apache2/sites-enabled/000-default.conf:1)
         port 80 namevhost seat.local (/etc/apache2/sites-enabled/000-default.conf:1)
         port 80 namevhost seat.local (/etc/apache2/sites-enabled/001-seat.conf:1)
                 alias www.seat.local
```

SeAT *should* now be available at http://seat.local/seat

##### Logrotate #####
SeAT logs a large amount of internals to 3 main log files:

   - `app/storage/logs/laravel.log`  
   - `app/storage/logs/pheal_access.log`  
   - `app/storage/logs/pheal_error.log`

Over time, these logs may explode in size (see [this](https://github.com/eve-seat/seat/issues/216)). While not a requirement for SeAT, it is however recommended that you setup logrotate for the log files. A sample configuration that you can dump into `/etc/logrotate.d/` is:

```bash
/var/www/seat/app/storage/logs/*.log {
    monthly
    missingok
    rotate 12
    compress
    notifempty
    create 750 www-data www-data
}
```

Ensure the path matches where you installed SeAT, and the user `www-data www-data` matches the user your web server is running as.

  [1]: http://laravel.com/
  [2]: http://www.php.net/
  [3]: http://httpd.apache.org/
  [4]: http://redis.io/
  [5]: http://supervisord.org/
  [6]: http://www.mysql.com/
