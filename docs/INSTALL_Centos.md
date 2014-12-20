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
A visual walkthrough of installing SeAT on CentOS is hosted on asciicinema and can be viewed [here](https://asciinema.org/a/14881). The walkthrough follows the exact same steps as detailed in the below guide, so feel free to follow along. It also shows that you can install SeAT in 16 minutes :D

### Installation Preparation ###
The following guide assumes you are using CentOS and are currently logged in as the `root` user. A separate guide for Debian / Ubuntu can be found in `docs/`.  
For CentOS, the EPEL repository is needed to satisfy the Redis and supervisord dependencies. We will have to add this repository early so lets get to that. Before you add the repository though, check that you have the `wget` command available for use by issuing it with `wget -V`:

```bash
[root@seat ~]# wget -V
GNU Wget 1.12 built on linux-gnu.
[... snip ...]
```

If you get a error such as `-bash: wget: command not found`, simply install `wget` using `yum` with `yum install -y wget`:

```bash
[root@seat ~]# yum install -y wget
Loaded plugins: fastestmirror
Setting up Install Process
Loading mirror speeds from cached hostfile
 * base: dl.za.jsdaav.net
 * extras: dl.za.jsdaav.net
 * updates: dl.za.jsdaav.net
Resolving Dependencies
--> Running transaction check
---> Package wget.x86_64 0:1.12-5.el6_6.1 will be installed
```

With `wget` ready to use, we can finally add the EPEL repository:

```bash
[root@seat ~]# EPEL=epel-release-6-8.noarch.rpm && BASEARCH=$(uname -i) && wget http://dl.fedoraproject.org/pub/epel/6/$BASEARCH/$EPEL -O $EPEL && yum localinstall -y $EPEL && rm -f $EPEL
```

#### 1. Install & Configure MySQL ####
Next, we will prepare a MySQL server instance for SeAT to use. If you already have a database server that you want to use, you may skip the installation section and move on to creating the database and user for SeAT.

##### MySQL Server #####
So, to install the database server, we run `yum install -y mysql-server`:

```bash
[root@seat ~]# yum install -y mysql-server
[... snip ...]
--> Running transaction check
---> Package mysql-server.x86_64 0:5.1.73-3.el6_5 will be installed
--> Processing Dependency: mysql = 5.1.73-3.el6_5 for package: mysql-server-5.1.73-3.el6_5.x86_64
--> Processing Dependency: perl-DBI for package: mysql-server-5.1.73-3.el6_5.x86_64
--> Processing Dependency: perl-DBD-MySQL for package: mysql-server-5.1.73-3.el6_5.x86_64
--> Processing Dependency: perl(DBI) for package: mysql-server-5.1.73-3.el6_5.x86_64
--> Running transaction check
---> Package mysql.x86_64 0:5.1.73-3.el6_5 will be installed
---> Package perl-DBD-MySQL.x86_64 0:4.013-3.el6 will be installed
---> Package perl-DBI.x86_64 0:1.609-4.el6 will be installed
--> Finished Dependency Resolution
[... snip ...]
Installed:
  mysql-server.x86_64 0:5.1.73-3.el6_5
```

With our MySQL server installed, we will configure it to automatically start should the server ever reboot with `chkconfig mysqld on`:

```bash
[root@seat ~]# chkconfig mysqld on
```

Next, we start the database server with `/etc/init.d/mysqld start`:

```bash
[root@seat ~]# /etc/init.d/mysqld start
[... snip ...]

                                                           [  OK  ]
Starting mysqld:                                           [  OK  ]
```

Lastly, we have to ensure that the database server is secure and that no unauthorized access is possible. MySQL comes shipped with a helper command for that, so we will invoke ith with `mysql_secure_installation`. Make sure you choose a strong MySQL `root` password and remember it:

```bash
[root@seat ~]# mysql_secure_installation

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
[root@seat ~]# mysql -uroot -p
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
Next we will prepare the web server along with PHP. If you plan on only using the SeAT for backend purposes then you can skip the `httpd` package.

So, lets install the dependencies with `yum install -y httpd php php-mysql php-cli php-mcrypt php-process php-mbstring`

```bash
[root@seat ~]# yum install -y httpd php php-mysql php-cli php-mcrypt php-process php-mbstring
[... snip ...]
Resolving Dependencies
--> Running transaction check
---> Package httpd.x86_64 0:2.2.15-39.el6.centos will be installed
--> Processing Dependency: httpd-tools = 2.2.15-39.el6.centos for package: httpd-2.2.15-39.el6.centos.x86_64
--> Processing Dependency: apr-util-ldap for package: httpd-2.2.15-39.el6.centos.x86_64
--> Processing Dependency: /etc/mime.types for package: httpd-2.2.15-39.el6.centos.x86_64
--> Processing Dependency: libaprutil-1.so.0()(64bit) for package: httpd-2.2.15-39.el6.centos.x86_64
--> Processing Dependency: libapr-1.so.0()(64bit) for package: httpd-2.2.15-39.el6.centos.x86_64
---> Package php.x86_64 0:5.3.3-40.el6_6 will be installed
--> Processing Dependency: php-common(x86-64) = 5.3.3-40.el6_6 for package: php-5.3.3-40.el6_6.x86_64
---> Package php-cli.x86_64 0:5.3.3-40.el6_6 will be installed
---> Package php-mbstring.x86_64 0:5.3.3-40.el6_6 will be installed
---> Package php-mcrypt.x86_64 0:5.3.3-3.el6 will be installed
--> Processing Dependency: libmcrypt.so.4()(64bit) for package: php-mcrypt-5.3.3-3.el6.x86_64
---> Package php-mysql.x86_64 0:5.3.3-40.el6_6 will be installed
--> Processing Dependency: php-pdo(x86-64) for package: php-mysql-5.3.3-40.el6_6.x86_64
---> Package php-process.x86_64 0:5.3.3-40.el6_6 will be installed
--> Running transaction check
---> Package apr.x86_64 0:1.3.9-5.el6_2 will be installed
---> Package apr-util.x86_64 0:1.3.9-3.el6_0.1 will be installed
---> Package apr-util-ldap.x86_64 0:1.3.9-3.el6_0.1 will be installed
---> Package httpd-tools.x86_64 0:2.2.15-39.el6.centos will be installed
---> Package libmcrypt.x86_64 0:2.5.8-9.el6 will be installed
---> Package mailcap.noarch 0:2.1.31-2.el6 will be installed
---> Package php-common.x86_64 0:5.3.3-40.el6_6 will be installed
---> Package php-pdo.x86_64 0:5.3.3-40.el6_6 will be installed
--> Finished Dependency Resolution

[... snip ...]

Installed:
  httpd.x86_64 0:2.2.15-39.el6.centos php.x86_64 0:5.3.3-40.el6_6 php-cli.x86_64 0:5.3.3-40.el6_6 php-mbstring.x86_64 0:5.3.3-40.el6_6 php-mcrypt.x86_64 0:5.3.3-3.el6 php-mysql.x86_64 0:5.3.3-40.el6_6 php-process.x86_64 0:5.3.3-40.el6_6
```

Next we ensire that the web server will automatically start should our server reboot (can be skipped if `httpd` was not installed) with `chkconfig httpd on`:

```bash
[root@seat ~]# chkconfig httpd on
[root@seat ~]#
```

And then lastly, we start Apache with `/etc/init.d/httpd start`:

```bash
[root@seat ~]# /etc/init.d/httpd start
Starting httpd: httpd: apr_sockaddr_info_get() failed for seat.local
httpd: Could not reliably determine the server's fully qualified domain name, using 127.0.0.1 for ServerName
                                                           [  OK  ]
```

#### 3. Install & Configure Redis ####
The SeAT Jobs system as well as caching occurs in Redis. At present this is the only supported backend for Queues and Cache. So just like we have been installing the other dependencies, we will do the same with `yum install -y redis`:

```bash
root@seat ~]# yum install -y redis
[... snip ...]
Resolving Dependencies
--> Running transaction check
---> Package redis.x86_64 0:2.4.10-1.el6 will be installed
--> Finished Dependency Resolution
[... snip ...]
Installed:
  redis.x86_64 0:2.4.10-1.el6
```

Next we enable it to start on boot with `chkconfig redis on`:

```bash
[root@seat ~]# chkconfig redis on
[root@seat ~]#
```

And start the Redis Cache with `/etc/init.d/redis start`

```bash
[root@seat ~]# /etc/init.d/redis start
Starting redis-server:                                     [  OK  ]
```

We now have all the required dependencies to install SeAT! :)

#### 4. Get SeAT ####

##### Preparing Git #####
The SeAT source code is hosted on [Github](https://github.com/eve-seat/seat) and to get a copy of it, we will need the `git` client. So, first check that you have `git` installed with `git --version`:

```bash
[root@seat ~]# git --version
git version 1.7.1
```

If you get a error such as `-bash: git: command not found`, simply install it using `yum` with `yum install -y git`:

```bash
[root@seat ~]# yum install -y git
[... snip ...]
Resolving Dependencies
--> Running transaction check
---> Package git.x86_64 0:1.7.1-3.el6_4.1 will be installed
--> Processing Dependency: perl-Git = 1.7.1-3.el6_4.1 for package: git-1.7.1-3.el6_4.1.x86_64
--> Processing Dependency: rsync for package: git-1.7.1-3.el6_4.1.x86_64
--> Processing Dependency: perl(Git) for package: git-1.7.1-3.el6_4.1.x86_64
--> Processing Dependency: perl(Error) for package: git-1.7.1-3.el6_4.1.x86_64
--> Running transaction check
---> Package perl-Error.noarch 1:0.17015-4.el6 will be installed
---> Package perl-Git.noarch 0:1.7.1-3.el6_4.1 will be installed
---> Package rsync.x86_64 0:3.0.6-12.el6 will be installed
--> Finished Dependency Resolution
[... snip ...]
Installed:
  git.x86_64 0:1.7.1-3.el6_4.1
```

##### Getting SeAT #####
Now we finally get to the part of downloading SeAT. This is also the time where you need to decide where you want SeAT to live on your server. I am going to use `/var/www` in the tutorial as the SELinux contexts will be very easy to setup.

So, change directories to `/var/www`:

```bash
[root@seat ~]# cd /var/www/
[root@seat www]#
```

If you check the directory listing with `ls`, you will notice a few folders already present:

```bash
[root@seat www]# ls
cgi-bin  error  html  icons
```

And finally, clone the SeAT repository from Github with `git clone https://github.com/eve-seat/seat.git`:

```bash
[root@seat www]# git clone https://github.com/eve-seat/seat.git
Initialized empty Git repository in /var/www/seat/.git/
remote: Counting objects: 6080, done.
remote: Compressing objects: 100% (165/165), done.
remote: Total 6080 (delta 87), reused 0 (delta 0)
Receiving objects: 100% (6080/6080), 2.25 MiB | 17 KiB/s, done.
Resolving deltas: 100% (4132/4132), done.
```

If you have to view the directory listing again now using `ls`, you should notice the new `seat` directory:

```bash
[root@seat www]# ls
cgi-bin  error  html  icons  seat
```

Change to the new SeAT directory using `cd seat` as we have some installation work to do here. You should be in the directory `/var/www/seat` after the `cd` command. This can be checked with the `pwd` command after the `cd`:

```bash
[root@seat www]# cd seat
[root@seat seat]# pwd
/var/www/seat
```

##### File Permissions #####

SeAT writes logfiles/cachefiles and other temporary data to the `app/storage` directory. That together with the fact that the web content will be hosted by apache means that we need to configure the files permissions to allow SeAT do do its thing.

First, lets ensure that apache owns everything in `/var/www/seat` which is the folder we just downloaded SeAT to with `chown -R apache:apache /var/www/seat`:

```bash
[root@seat seat]# chown -R apache:apache /var/www/seat
[root@seat seat]#
```

Next, we will allow Apache to write to the `app/storage` directory so that it may manipulate the files in there as needed with `chmod -R guo+w /var/www/seat/app/storage`:

```bash
[root@seat seat]# chmod -R guo+w /var/www/seat/app/storage
[root@seat seat]#
```

SeAT is now downloaded and almost ready to start being useful!

#### 5. SeAT Configuration File ####
Some of the SeAT settings need to live in a configuration file. Thankfully, this is not one you have to edit manually, but you do need to provide a sample for the SeAT installer to use later. A sample file lives in `app/config/env-sample.php`, and needs to be copied to `.env.php` in your `seat/` folder. So, while you are still in `/var/www/seat`, copy the sample with `cp app/config/env-sample.php .env.php`:

```bash
[root@seat seat]# pwd
/var/www/seat
[root@seat seat]# cp app/config/env-sample.php .env.php
[root@seat seat]#
```

#### 5. The Composer Dependency Manager ####
SeAT relies heavily on [composer](https://getcomposer.org/) to manage its internal dependencies. `composer` is a command line tool that should be downloaded separately. Once downloaded, we will have a file called `composer.phar`, which will be executed to update the SeAT dependencies amongst other things.

So, lets install `composer`. We can store the file in the same folder where our `composer.json` lives, which will therefore be `/var/www/seat` (make sure that is the directory you are in before downloading). Then we download `composer` with `curl -sS https://getcomposer.org/installer | php`:

```bash
[root@seat seat]# curl -sS https://getcomposer.org/installer | php
#!/usr/bin/env php
Some settings on your machine may cause stability issues with Composer.
If you encounter issues, try to change the following:

Your PHP (5.3.3) is quite old, upgrading to PHP 5.3.4 or higher is recommended.
Composer works with 5.3.2+ for most people, but there might be edge case issues.

Downloading...

Composer successfully installed to: /var/www/seat/composer.phar
Use it: php composer.phar
```

With `composer` now ready to use, we start the dependency installation with `php composer.phar install` (`composer.phar` is in the same directory as `composer.json` in the below example). Note that this could take some time to complete:

```bash
[root@seat seat]# php composer.phar install
Loading composer repositories with package information
Installing dependencies (including require-dev)
  - Installing 3rdpartyeve/phealng (1.3.0)
    Downloading: 100%
    
[... snip ...]

Generating autoload files
Generating optimized class loader
Compiling common classes
[root@seat seat]#
```

** NEARLY THERE **

#### 5. The SeAT Installer ####
The next step is to invoke the SeAT installer. The installer is responsible for ensuring that the configuration files are correct, the SeAT install has been configured for email and that the database is ready for use.

**Installer Notes:** The installer the only continue once it has successfully made a connection to a MySQL database and a Redis cache. Please ensure that these have already been configured as per the previous steps.  
**Notes about mail:** The SeAT installer will provide you with 3 options for email. `mail`, which will use PHP's mailer, `smtp`, which will prompt you for optional credentials and `sendmail`.

All settings made by the installer may be changed at a later state as they live in the `.env.php` file.
So, lets invoke the installer with `php artisan seat:install`:

```bash
[root@seat seat]# php artisan seat:install
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

So lets install supervisord with `yum install -y supervisor`:

```bash
root@seat seat]# yum install -y supervisor
[... snip ...]
Resolving Dependencies
--> Running transaction check
---> Package supervisor.noarch 0:2.1-9.el6 will be installed
--> Processing Dependency: python-meld3 for package: supervisor-2.1-9.el6.noarch
--> Running transaction check
---> Package python-meld3.x86_64 0:0.6.7-1.el6 will be installed
--> Finished Dependency Resolution
[... snip ...]
Installed:
  supervisor.noarch 0:2.1-9.el6
```

Once it is installed, we also want it to start when the server boots, so lets do that with `chkconfig supervisord on`:

```bash
[root@seat seat]# chkconfig supervisord on
[root@seat seat]#
```

We now have to configure the actual workers that `supervisord` will manage. We go this by adding `program` configuration blocks to `/etc/supervisord.conf`. A sample configuration for this block is located in `docs/` and at the end of this paragraph and can simply be added to the bottom of the existing configuration file. Note that the sample has the program defined as `[program:seat1]`. If you want to run 4 workers, you need to add this to the `supervisord.conf` *4* times. So you will have 4 blocks with incrementing numbers ie. `[program:seat1]`, `[program:seat2]`, `[program:seat3]` & `[program:seat4]`.

```bash
[program:seat1]
command=/usr/bin/php /var/www/seat/artisan queue:listen --timeout=3600 --tries=1
directory=/var/www/seat
stopwaitsecs=600
user=apache
stdout_logfile=/var/log/seat_out.log
stdout_logfile_maxbytes=100MB
stdout_logfile_backups=10
stderr_logfile=/var/log/seat_err.log
stderr_logfile_maxbytes=100MB
stderr_logfile_backups=5
```

Once this is done, save the file and start supervisord with `/etc/init.d/supervisord start`:

```bash
[root@seat seat]# /etc/init.d/supervisord start
Starting supervisord:                                      [  OK  ]
```

You can checkup on the status of the workers with `supervisorctl status`:

```bash
[root@seat seat]# supervisorctl status
seat1          RUNNING    pid 23583, uptime 1 day, 0:18:27
seat2          RUNNING    pid 23584, uptime 1 day, 0:18:27
seat3          RUNNING    pid 23585, uptime 1 day, 0:18:27
seat4          RUNNING    pid 23582, uptime 1 day, 0:18:27
```

SeAT should now process jobs that enter the job queue.

####  9. Setup cron ####
While we now have workers ready, and a supervisor for them, we need to configure the part that is responsible for generating the work. SeAT has a preconfigured schedule at which work will come in, however, we need to check every minute of there is work to do. For that we setup a simple cronjob for the web server user with `crontab -u apache -e`, adding the following line to it `* * * * * /usr/bin/php /var/www/seat/artisan scheduled:run 1>> /dev/null 2>&1`:

```bash
[root@seat seat]# crontab -u apache -e
# paste * * * * * /usr/bin/php /var/www/seat/artisan scheduled:run 1>> /dev/null 2>&1
```

#### 10. SELinux #####
Many people hate SELinux, primarily due to a misunderstanding of what it does and how it works. SeAT can run perfectly fine with SELinux enabled, and I actually encourage you to leave it enabled. There is however one small settings change required to make everything work as expected.

First, we have to allow apache to make network connections. This is so that we may connect to the EVEAPI, as well as the MySQL database and Redis cache. Allow this to happen with `setsebool -P httpd_can_network_connect 1`:

```bash
[root@seat seat]# setsebool -P httpd_can_network_connect 1
[root@seat seat]#
```

Next, we check that all of the SeAT files are labelled correctly so that the SELinux MAC does not incorrectly deny access. We do this with `restorecon -Rv /var/www/seat`:
```bash
[root@seat seat]# restorecon -Rv /var/www/seat
[root@seat seat]#
```

Done! :)

#### 11. Web Server ####
In order to get the SeAT fronted running, we need to configure Apache to serve our SeAT installs `public/` folder.  
The Apache configuration itself will depend on how your server is set up. Generally, virtual hosting is the way to go, and this is what I will be showing here.

If you are not going to use virtual hosting, the easiest to get going will probably to symlink `/var/www/seat/public/` to `/var/www/html/seat` and configuring apache to `AllowOverride All` in the `<Directory "/var/www/html">` section. This should have SeAT available at http://hostname/seat after you restart apache.

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

With that done, we continue to configure Apache for our VirtualHost. First we change to the directory `/etc/httpd/conf.d` which will have our `seat.local.conf` configuration file with `cd /etc/httpd/conf.d`:

```bash
[root@seat seat]# cd /etc/httpd/conf.d
[root@seat conf.d]#
```

Next, we create the file `seat.local`, pasting the following contents into it:

```bash
<VirtualHost *:80>
    ServerAdmin webmaster@your.domain
    DocumentRoot "/var/www/html/seat.local"
    ServerName seat.local
    ServerAlias www.seat.local
    ErrorLog "logs/seat.local-error_log"
    CustomLog "logs/seat.local-access_log" common
    <Directory "/var/www/html/seat.local">
        AllowOverride All
        Order allow,deny
        Allow from all
    </Directory>
</VirtualHost>
```

With the configuration file ready, we will restart Apache, and check if it sees our new Virtual Host. Restart apache with `apachectl restart`:

```bash
[root@seat conf.d]# apachectl restart
httpd: apr_sockaddr_info_get() failed for seat.local
httpd: Could not reliably determine the server's fully qualified domain name, using 127.0.0.1 for ServerName
[root@seat conf.d]#
```

Lastly, confirm the our virtual host is present with `apachectl -t -D DUMP_VHOSTS`:

```bash
[root@seat conf.d]# apachectl -t -D DUMP_VHOSTS
httpd: apr_sockaddr_info_get() failed for seat.local
httpd: Could not reliably determine the server's fully qualified domain name, using 127.0.0.1 for ServerName
VirtualHost configuration:
wildcard NameVirtualHosts and _default_ servers:
*:80                   seat.local (/etc/httpd/conf.d/seat.local.conf:1)
Syntax OK
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
    create 750 apache apache
}
```

Ensure the path matches where you installed SeAT, and the user `apache apache` matches the user your web server is running as.

  [1]: http://laravel.com/
  [2]: http://www.php.net/
  [3]: http://httpd.apache.org/
  [4]: http://redis.io/
  [5]: http://supervisord.org/
  [6]: http://www.mysql.com/
