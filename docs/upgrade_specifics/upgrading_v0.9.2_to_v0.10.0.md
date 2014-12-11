## Upgrade Notes for SeAT v0.9.2 -> v0.10.0

This guide details special steps that you should take to ensure a clean and successful upgrade.

After you have followed the normal steps found in `docs/UPGRADING.md`, and before you run `php artisan up`, follow the next few steps to ensure that your installation is upgraded completely.

### Version 0.9.2 -> 0.10.0

If you are running CentOS, ensure that you have `php-mbstring` installed. Once installed, restart Apache.
  - `yum install php-mbstring`

Run the `SeatSettingSeeder` to prepare the database for the SeAT UI settings with `php artisan db:seed --class SeatSettingSeeder`.

Lastly, bring your app back online by running `php artisan up` and watch the log files for any potential errors.

#### Errr, I think it broke. HALP!
Come on to [irc](https://kiwiirc.com/client/irc.coldfront.net/?nick=seat_user%7C?#wcs-pub) and lets see if we can fix any problems you may have.
