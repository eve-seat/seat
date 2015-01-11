## Upgrade Notes for SeAT v0.12.x -> v0.13.0

This guide details special steps that you should take to ensure a clean and successful upgrade.

After you have followed the normal steps found in `docs/UPGRADING.md`, and before you run `php artisan up`, follow the next few steps to ensure that your installation is upgraded completely.

### Version 0.12.x -> 0.13.0

SeAT 0.13.0 has moved more configuration to the recently introduced `.env.php` configuration file. Some users still had to edit `app/config/mail.php` in order to get functional email for their installation.
In order to ease the configuration and future upgrades, 2 new variables have been added to the `.env.php` configuration. The values for `smtp_username` and `smtp_password` now live in the `.env.php` configuration.

#### Moving the required configuration

View the sample configuration file [here](https://github.com/eve-seat/seat/blob/dev/app/config/env-sample.php). Take special note of `smtp_username` and `smtp_password`. Using the sample env file, copy lines 16 and 17 into your local `.env.php` file.
If you required these to have functional email, modify them accordingly, else, leaving the values to `NULL` is perfectly fine.

#### Corporation Security Update

SeAT 0.13.0 introduced some visual upgrades to the way corporation roles are displayed. Some minor static data needs to be re-seeded to the database for this.
Re-seed the corporation role mappings with:
`php artisan db:seed --class=EveCorporationRolemapSeeder`

Lastly, bring your app back online by running `php artisan up` and watch the log files for any potential errors.

#### Errr, I think it broke. HALP!
Come on to [irc](https://kiwiirc.com/client/irc.coldfront.net/?nick=seat_user%7C?#wcs-pub) and lets see if we can fix any problems you may have.
