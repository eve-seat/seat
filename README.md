## SeAT - Simple (or Stupid) EVE Online API Tool ##
[![Latest Stable Version](https://poser.pugx.org/eve-seat/seat/v/stable.svg)](https://packagist.org/packages/eve-seat/seat) [![Total Downloads](https://poser.pugx.org/eve-seat/seat/downloads.svg)](https://packagist.org/packages/eve-seat/seat) [![Latest Unstable Version](https://poser.pugx.org/eve-seat/seat/v/unstable.svg)](https://packagist.org/packages/eve-seat/seat) [![License](https://poser.pugx.org/eve-seat/seat/license.svg)](https://packagist.org/packages/eve-seat/seat)

##### *SeAT is still under heavy development and is not considered 100% stable yet. USE AT YOUR OWN RISK* #####

### Introduction ###
SeAT is a EVE Online Corporation Management Tool written in PHP using the [Laravel][1] Framework driven by a MySQL database.
SeAT itself is the result of a rewrite of the original Corporation Management Tool that I wrote for our corp and figured there may be others out there that may need similar tools.

### Features ###
SeAT allows corporation CEO's and directors to manage member API keys, store member wallet & mail history, monitor corporaton poses, wallets, transactions, account ledgers etc.

### Technical Summary ###
API Keys are stored in the backend database and get updated as the schedule is configured. A cronjob gets kicked off every minute that checks which jobs need to be scheduled and actions as required.
A 'job' can be defined as a set of categorized API calls to update certain part of a Character, Corporation, Eve or Map related information in the backend. More than 55 API Endpoints have been implemented and form part of these jobs.

The SeAT backend can be run completely independant (without the frontend) and simply used to keep the database up to date, using the data in our own tools.

### Demo Site ###
You can test out SeAT at [http://eveseat.com/](http://eveseat.com/)

### Screenshots and More Information ###
Check out the SeAT [feature page](http://eve-seat.github.io/) for a visual taste of what SeAT has to offer, as well as a feature breakdown.

### Installation ###
Refer to the `docs/` directory for installation instructions. It is suggested that you checkout the latest release that can be found [here](https://github.com/eve-seat/seat/releases).

### Upgrading ###
Please refer to the UPGRADING guide in `docs/UPDGRADING.md`

### Todo ###
There is a metric ton of information pulled via the API that is not yet exposed on the web front end... so *lots* to do still :)

A much longer term goal would be to get the system to such a state where corporation members are able to register and view thier own keys and administrators are able to delegate roles such as recruiters etc.

### Contact ###
You can get hold of me via Twitter [@qu1ckkkk](https://twitter.com/qu1ckkkk), ingame character [qu1ckkkk](http://evewho.com/pilot/qu1ckkkk) or on [IRC](https://kiwiirc.com/client/irc.coldfront.net/?nick=seat_user|?#wcs-pub) at #wcs-pub on irc.coldfront.net

  [1]: http://laravel.com/

### Legal ###
EVE Online and the EVE logo are the registered trademarks of CCP hf. All rights are reserved worldwide. All other trademarks are the property of their respective owners. EVE Online, the EVE logo, EVE and all associated logos and designs are the intellectual property of CCP hf. All artwork, screenshots, characters, vehicles, storylines, world facts or other recognizable features of the intellectual property relating to these trademarks are likewise the intellectual property of CCP hf.
