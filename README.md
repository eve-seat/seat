## SeAT - Simple (or Stupid) EVE Online API Tool ##

[![Latest Release](http://img.shields.io/github/release/eve-seat/seat.svg?style=flat)](https://github.com/eve-seat/seat/releases/latest)

##### *This is very much BETA software with a ton of things not yet implemented. USE AT YOUR OWN RISK* #####

### Introduction ###
SeAT attempts to be a EVE Online™ Corporation Management Tool written in PHP using the [Laravel][1] Framework driven by a MySQL database.  
The SeAT backend is *highly* influenced by YAPEAL. SeAT itself is the result of a rewrite of the original Corporation Management Tool that I wrote for our corp and figured there may be others out there that may need similar tools.

### Features ###
SeAT allows corporation CEO's and directors to manage member API keys, store member wallet & mail history, monitor corporaton poses, wallets, transactions, account ledgers etc.

### Technical Summary ###
API Keys are stored in the backend database and get updated as the schedule is configured. A cronjob gets kicked off every minute that checks which jobs need to be scheduled and actions as required.  
A 'job' can be defined as a set of categorized API calls to update certain part of a Character, Corporation, Eve or Map related information in the backend. More than 55 API Endpoints have been implemented and form part of these jobs.

### Screenshots ###

Character View
![Character View](http://i.imgur.com/vKkE7bv.png)

Key Details View
![Key Details View](http://i.imgur.com/DUQCP7q.png)

Starbase Details View
![Starbase Details View](http://i.imgur.com/es3uIkA.png)

### Installation ###
Refer to the `docs/` directory for installation instructions. It is suggested that you checkout the latest release that can be found [here](https://github.com/eve-seat/seat/releases).

### Upgrading ###
Please refer to the UPGRADING guide in `docs/UPDGRADING.md`

### Todo ###
There really is a TON of stuff that still needs to be done:

- ~~Corporation Wallet Ledger~~
- ~~Asset Search~~
- ~~Skill Search~~
- ~~Clone Overview~~
- Character Interactions (ie Player Tradings, Mails etc.)
- ~~Starbase Fuel Time Left calculations~~ as well as Silo input/output end etas.
- Corporation Sheets with Member Security Roles view

There is a metric ton of information pulled via the API that is not yet exposed on the web front end... so *lots* to do still :)

A much longer term goal would be to get the system to such a state where corporation members are able to register and view thier own keys and administrators are able to delegate roles such as recruiters etc.

### Contact ###
You can get hold of me via Twitter [@qu1ckkkk](https://twitter.com/qu1ckkkk), ingame character [qu1ckkkk](http://evewho.com/pilot/qu1ckkkk) or on [IRC](https://kiwiirc.com/client/irc.coldfront.net/?nick=seat_user|?#wcs-pub) at #wcs-pub on irc.coldfront.net

  [1]: http://laravel.com/

### Legal ###
EVE Online and the EVE logo are the registered trademarks of CCP hf. All rights are reserved worldwide. All other trademarks are the property of their respective owners. EVE Online, the EVE logo, EVE and all associated logos and designs are the intellectual property of CCP hf. All artwork, screenshots, characters, vehicles, storylines, world facts or other recognizable features of the intellectual property relating to these trademarks are likewise the intellectual property of CCP hf.
