## Upgrade Notes for SeAT v0.7 -> v0.8

This guide details special steps that you should take to ensure a clean and successful upgrade.

After you have followed the normal steps found in `docs/UPGRADING.md`, and before you run `php artisan up`, follow the next few steps to ensure that your installation is upgraded completely.

### Version 0.7 -> 0.8

2 New table seeds have to be run. Do this by running the following commands in your SeAT directory:  

  - `php artisan db:seed --class EveCorporationRolemapSeeder`  
  - `php artisan db:seed --class EveApiCalllistTableSeeder`  
    
A new dependency has been introduced as well and should have been installed when you ran `composer update` in the normal steps found in `docs/UPGRADING.md`. Ensure you have done this. 

Lastly, bring your app back online by running `php artisan up` and watch the log files for any potential errors.

#### Errr, I think it broke. HALP!
Come on to [irc](https://kiwiirc.com/client/irc.coldfront.net/?nick=seat_user%7C?#wcs-pub) and lets see if we can fix any problems you may have.
