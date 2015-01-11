# How to upgrade SeAT

The following guide attempts to be a very generic walk through for upgrading SeAT to the latest release. Theoretically speaking, this will normally be all the steps required to get up and running again. Some steps may technically not be necessary, but they wont hurt your install.

**NOTE:** As most good sysadmins do, I would recommend you *backup* your database before doing any actual upgrade work, though generally the database migrations attempt to be non destructive.

## Upgrading

*Note:* Since version 0.13.0, a new experimental SeAT updater was added that should automate most of the below work. This upgrade option is **not** aware of _any_ version specific upgrade steps and these should still be read before using this tool. For now, the SeAT updater should be considered experimental. If you wish to use this instead of the below manual steps, issue the `php artisan seat:update` command.

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
```

If you get a error such as `error: You have local changes to 'app/config/app.php'; cannot switch branches.`, it normally means that your configuration files are conflicting with those on the new branch. We can get past this by first doing a `git stash`, saving our changes. Then `git pull`. Then `git stash pop`, to re-apply your changes. **NB Note:** Make sure you read the release notes about each version as some may have changes to the files that you have changed.

* Ensure that you have the latest vendor packages. Assuming you have the composer.phar saved in the project root it may look something like this:

```bash
$ php /var/www/seat/composer.phar update
Loading composer repositories with package information
Updating dependencies (including require-dev)
Generating autoload files
Generating optimized class loader
```

* Regenerate the composer autoload classes:

```bash
$ php /var/www/seat/composer.phar dump-autoload
Generating autoload files
```

* Run any outstanding database migrations.

```bash
$ php artisan migrate
Migrated: 2014_04_16_173335_LaravelUpdate4_1_26_RememberMe  # will show 'Nothing to migrate.' if nothing is outstanding
```

* Ensure that you have the latest EVE SDE's

```bash
$ php artisan seat:update-sde
Warning! This Laravel command uses exec() to execute a mysql shell command to import a extracted dump.
Due to the way the command is constructed, should someone view the current running processes of your server, they will be able to see your SeAT database users password.

Ensure that you understand this before continuing.
Are you sure you want to update to the latest EVE SDE? [yes|no] y
Checking for SDE updates at https://raw.githubusercontent.com/eve-seat/seat/resources/sde_version.json ...
The current SDE version is phoebe-1.0-107269
19 dumps will be downloaded from https://www.fuzzwork.co.uk/dump/phoebe-1.0-107269/ in .sql.bz2 format and imported into mysql://127.0.0.1/seat

[.. snip ..]
```

* Check if the version you are upgrading to has any special instructions in `docs/upgrade_specifics`. Eg. SeAT v0.7 -> v0.8 had some extra table seeds to be run, and includes the instructions in a file for this.

* Bring the application out of maintenance mode:

```bash
$ php artisan up
Application is now live.
```

## Notes
Generally its a good idea to have a look at `app/storage/logs/laravel.log` to check if anything fishy has gone on and possibly pick up on any errors early on.

## Additional Resources

* #wcs-pub IRC channel on coldfront.net
