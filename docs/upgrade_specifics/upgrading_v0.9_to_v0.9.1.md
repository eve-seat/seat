## Upgrade Notes for SeAT v0.8 -> v0.9

This guide details special steps that you should take to ensure a clean and successful upgrade.

After you have followed the normal steps found in `docs/UPGRADING.md`, and before you run `php artisan up`, follow the next few steps to ensure that your installation is upgraded completely.

### Version 0.9 -> 0.9.1

You must seed the default SeAT groups:
  - `php artisan seat:groupsync`

```bash
$ php artisan seat:groupsync
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
```

Lastly, bring your app back online by running `php artisan up` and watch the log files for any potential errors.

#### Errr, I think it broke. HALP!
Come on to [irc](https://kiwiirc.com/client/irc.coldfront.net/?nick=seat_user%7C?#wcs-pub) and lets see if we can fix any problems you may have.
