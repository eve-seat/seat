#Troubleshooting SeAT

This guide aims to provide basic troubleshooting tips for SeAT for common problems. At any point if you are unsure or want more help feel free to join the IRC channel `#wcs-pub` IRC channel on coldfront.net where there is normally someone around to help you out. 

Table of Contents

* [How to clear the cache and trigger an update](#clearcache)
* [Where are the logs stored](#logs)
* [I'm not receiving e-mails](#emails)

<a name="clearcache"></a>
##How to clear the cache and trigger an update

If you believe there might be an issue with the cache, particularly when API keys seem to be having issues, clearing the cache is a safe troubleshooting method which can go a long way in diagnosing or even fixing the problem. 

To clear the cache navigate to the directory you installed SeAT in (likely `var/www/seat`) and run the following command: 

```bash
$ php artisan seat:clear-cache
```

You can then wait for SeAT to update the API keys, or you can trigger an update with the following command:

```bash
$ php artisan seat:api-update
```

<a name="logs"></a>
##Where are the logs stored

SeAT logs lots of things, making the logs extremely useful for troubleshooting. The logs are stored in the following directory inside your SeAT install: 

```
/app/storage/logs
```

There are three logs: 
* laravel.log - Contains SeATs logs, including any issues with the application etc... This is the most important one and where you will most likely find anything related to your issue
* pheal_access.log & pheal_error.log - Pheal is the library used to access the EVE API and so these logs contain any issues related to the API 

SeAT rolls (archives) the logs at the end of every month; ensure you are looking at the relevant log. 

If you are unsure what the logs mean, try and find the relevant part of the log and put it in ![Pastebin](http://pastebin.com/) and link this in the IRC channel so we can help you faster!

<a name="emails"></a>
##I'm not receiving e-mails

If you have installed SeAT, but you are not receiving e-mails the most common culprit is that the e-mail settings are incorrect. Open the `.env.php` file located in the root of your SeAT install and verify your e-mail settings are correct. It is important to check what mail driver you are using (detailed in the install docs). 
