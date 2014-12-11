## Upgrade Notes for SeAT v0.10.x -> v0.11.0

This guide details special steps that you should take to ensure a clean and successful upgrade.

After you have followed the normal steps found in `docs/UPGRADING.md`, and before you run `php artisan up`, follow the next few steps to ensure that your installation is upgraded completely.

### Version 0.10.x -> 0.11.0

*Hopefully you are reading this before you have upgraded! If not, its not a train smash but it is preferred.*

SeAT 0.11.0 introduces a new way to manage site configuration. Due to this change, some manual intervention is required.

In summary, we are now making use of environment files, and referencing them in the Laravel configuration. Due to the fact that you probably don't have a `.env.php` file, you will need to create this one manually.

#### Preparing the new configuration

Create the file `.env.php` in the same folder as `composer.json` with the following contents:

```php
<?php // SeAT Sample .env.php

return array (
  'mysql_hostname' => '127.0.0.1',
  'mysql_database' => 'seat',
  'mysql_username' => 'root',
  'mysql_password' => '',
  'redis_host' => '127.0.0.1',
  'redis_port' => 6379,
  'mail_driver' => 'mail',
  'mail_from' => 'seatadmin@localhost',
  'mail_from_name' => 'SeAT Administrator',
  'smtp_hostname' => '127.0.0.1',
  'smtp_username' => NULL,
  'smtp_password' => NULL,
);
```

Next, you need to fill the values in the new `.env.php` with the correct ones from `app/config/database.php` as well as `app/config/mail.php`.

Once your `.env.php` file is populated with the correct values, checkout the repo versions of `app/config/database.php` and `app/config/mail.php` with:

```bash
$ git checkout -- app/config/database.php
$ git checkout -- app/config/mail.php
```

Now, upgrade your application.

*Note* If you are reading this after you upgraded, do not dispair. Worst case you will need to reset the password for the SeAT database account if you cant rememeber it.

Lastly, bring your app back online by running `php artisan up` and watch the log files for any potential errors.

#### Errr, I think it broke. HALP!
Come on to [irc](https://kiwiirc.com/client/irc.coldfront.net/?nick=seat_user%7C?#wcs-pub) and lets see if we can fix any problems you may have.
