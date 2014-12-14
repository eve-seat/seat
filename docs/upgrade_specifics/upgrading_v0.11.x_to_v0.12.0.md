## Upgrade Notes for SeAT v0.11.x -> v0.12.0

This guide details special steps that you should take to ensure a clean and successful upgrade.

After you have followed the normal steps found in `docs/UPGRADING.md`, and before you run `php artisan up`, follow the next few steps to ensure that your installation is upgraded completely.

### Version 0.11.x -> 0.12.0

SeAT 0.12.0 saw some **significant** changes to the backed from a authentication and permissions perspective. In short, we threw out [Sentry](https://github.com/cartalyst/sentry) in favour of the native Laravel [Auth Driver](http://laravel.com/docs/4.1/security#authenticating-users). Throwing out Sentry meant that we lose the groups/permissions features we were leveraging off and as a result, had to write this ourselves.

To the average user this may not have any implication, but it does however allow developers to take more control of how access is controlled within SeAT.

#### Preparing the move

Since the Authentication logic has changed, the location of where user accounts has also changed. Luckily, the password hashing scheme remains the same, so it simply a case of copying these over to the new table. Thankfully, a command was added to help with this.

There is also a change to `app/config/app.php`, which means you need to checkout the default from the current tag with `git checkout -- app/config/app.php`, and re-apply the changes post-upgrade.

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

Lastly, bring your app back online by running `php artisan up` and watch the log files for any potential errors.

#### Errr, I think it broke. HALP!
Come on to [irc](https://kiwiirc.com/client/irc.coldfront.net/?nick=seat_user%7C?#wcs-pub) and lets see if we can fix any problems you may have.
