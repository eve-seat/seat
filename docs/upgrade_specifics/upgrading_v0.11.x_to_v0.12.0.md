## Upgrade Notes for SeAT v0.11.x -> v0.12.0

This guide details special steps that you should take to ensure a clean and successful upgrade.

After you have followed the normal steps found in `docs/UPGRADING.md`, and before you run `php artisan up`, follow the next few steps to ensure that your installation is upgraded completely.

### Version 0.11.x -> 0.12.0

SeAT 0.12.0 saw some **significant** changes to the backed from a authentication and permissions perspective. In short, we threw out [Sentry](https://github.com/cartalyst/sentry) in favour of the native Laravel [Auth Driver](http://laravel.com/docs/4.1/security#authenticating-users). Throwing out Sentry meant that we lose the groups/permissions features we were leveraging off and as a result, had to write this ourselves.

To the average user this may not have any implication, but it does however allow developers to take more control of how access is controlled within SeAT.

#### Preparing the move

Since the Authentication logic has changed, the location of where user accounts has also changed. Luckily, the password hashing scheme remains the same, so it simply a case of copying these over to the new table. Thankfully, a command was added to help with this.



#### Moving accounts

So, to move the accounts, all you need to do (after you have pulled the latest code, ran migrations and updated dependecies) is run:

```bash
λ git dev → php artisan seat:user-migrate

WARNING!!! This will OVERWRITE any user accounts created since upgrading to \Auth!

Please note: Any users which have registered but not activated their account will
need to re-register.

Are you sure you want to migrate all users? [yes|no]
```

Selecting yes, will read the information from the old Sentry tables and populate the users, groups and permissions accordingly in the new `\Auth` scheme.

Once this completes, run the groupsync to ensure all the other default groups are back too:

```bash
λ git dev → php artisan seat:groupsync
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
[info] Group Key Manager was not found. Creating it.
[ok] Group Key Manager created.
```

#### App Config Change b removing Facades

There is a change to `app/config/app.php`, which means you need to checkout the default from the current tag with `git checkout -- app/config/app.php`, and re-apply the changes post-upgrade.

#### Setting Migration Bug

The migration script that created the settings table had a bug where it was not applying a unique index to the setting name. So, for this to be fixed you can run the following SQL command in your MySQL prompt on the `seat` database (or whatever you called it):

```sql
TRUNCATE TABLE `seat_settings`;
ALTER TABLE `seat_settings` ADD UNIQUE INDEX (`setting`);
```

A sample run doing this will be:

```bash
$ mysql -u root -p
Enter password:
Welcome to the MySQL monitor.  Commands end with ; or \g.
Your MySQL connection id is 303807
Server version: 5.1.73 Source distribution

Copyright (c) 2000, 2013, Oracle and/or its affiliates. All rights reserved.

Oracle is a registered trademark of Oracle Corporation and/or its
affiliates. Other names may be trademarks of their respective
owners.

Type 'help;' or '\h' for help. Type '\c' to clear the current input statement.

mysql> use seat;
Reading table information for completion of table and column names
You can turn off this feature to get a quicker startup with -A

Database changed
mysql> TRUNCATE TABLE `seat_settings`;
Query OK, 0 rows affected (0.01 sec)

mysql> ALTER TABLE `seat_settings` ADD UNIQUE INDEX (`setting`);
Query OK, 0 rows affected (0.01 sec)
Records: 0  Duplicates: 0  Warnings: 0

mysql> \q
Bye
```

Lastly, bring your app back online by running `php artisan up` and watch the log files for any potential errors.

#### Errr, I think it broke. HALP!
Come on to [irc](https://kiwiirc.com/client/irc.coldfront.net/?nick=seat_user%7C?#wcs-pub) and lets see if we can fix any problems you may have.
