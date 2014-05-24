# How to upgrade SeAT

The following guide attempts to be a very generic walk through for upgrading SeAT to the latest release. Theoretically speaking, this will normally be all the steps required to get up and running again. Some steps may technically not be necessary, but they wont hurt your install.

**NOTE:** As most good sysadmins do, I would recommend you *backup* your database before doing any actual upgrade work though generally the database migrations attempt to be non destructive.

## Upgrading

* Ensure you have backups ready :D
* Change directory to your installs root. This may be something like `/var/www/seat`.
* Place your application into maintenance mode:

```bash
$ php artisan down
Application is now in maintenance mode.
```
* Check if any jobs are currently being processed. Placing the application into maintenance mode will stop the workers from picking up any new jobs, but currently running ones will continue until they are done. You can watch the current working jobs on the command line with:

```bash
$ php artisan seat:queue-status
Redis reports 0 jobs in the queue (queues:default)
The database reports 0 queued jobs
The database reports 42043 done jobs
The database reports 0 error jobs
The database reports 0 working jobs
```

* Once no more jobs are running, get the latest version of the code from github. You can check the releases [here](https://github.com/eve-seat/seat/tags) and use the latest tagged version:

```bash
$ git fetch
$ git pull
$ git checkout tags/v0.1    # sample version
```
* Ensure that you have the latest vendor packages by running `composer update`. Assuming you have the composer.phar saved in the project root it may look something like this:

```bash
$ /var/www/vhosts/seat/composer.phar update
Loading composer repositories with package information
Updating dependencies (including require-dev)
Generating autoload files
Generating optimized class loader
```

* Regenerate the composer autoload classes:

```bash
$ /var/www/vhosts/seat/composer.phar dump-autoload
Generating autoload files
```

* Run any outstanding database migrations.

```bash
$ php artisan migrate
Migrated: 2014_04_16_173335_LaravelUpdate4_1_26_RememberMe  # will show 'Nothing to migrate.' if nothing is outstanding
```

* Check if the version you are upgrading to has any special instructions in `docs/upgrade_specifics`. Eg. SeAT v0.7 -> v0.8 had some extra table seeds to be run, and includes the instructions in a file for this.

* Bring the application out of maintenance mode:

```bash
$ php artisan up
Application is now live.
```

## Notes
Generally its a good idea to have a look at `app/storage/logs/laravel.log` to check if anything fishy has going on and possibly pick up on errors early.

## Additional Resources

* #wcs-pub IRC channel on coldfront.net
