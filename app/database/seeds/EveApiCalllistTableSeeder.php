<?php
/*
The MIT License (MIT)

Copyright (c) 2014 eve-seat

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/

class EveApiCalllistTableSeeder extends Seeder
{

    public function run()
    {

        // Temporarily disable mass assignment restrictions
        Eloquent::unguard();

        DB::table('api_calllist')->delete();

        // Eve Api Callist from https://api.eveonline.com/api/calllist.xml.aspx
        // Taken Sat, 22 Mar 2014 06:47:37 +0000
        EveApiCalllist::create(array('type' => 'Character', 'name' => 'Locations', 'accessMask' => 134217728, 'description' => 'Allows the fetching of coordinate and name data for items owned by the character.'));
        EveApiCalllist::create(array('type' => 'Character', 'name' => 'Contracts', 'accessMask' => 67108864, 'description' => 'List of all Contracts the character is involved in.'));
        EveApiCalllist::create(array('type' => 'Character', 'name' => 'AccountStatus', 'accessMask' => 33554432, 'description' => 'EVE player account status.'));
        EveApiCalllist::create(array('type' => 'Character', 'name' => 'CharacterInfo', 'accessMask' => 16777216, 'description' => 'Sensitive Character Information, exposes account balance and last known location on top of the other Character Information call.'));
        EveApiCalllist::create(array('type' => 'Character', 'name' => 'CharacterInfo', 'accessMask' => 8388608, 'description' => 'Character information, exposes skill points and current ship information on top of \'Show Info\' information.'));
        EveApiCalllist::create(array('type' => 'Character', 'name' => 'WalletTransactions', 'accessMask' => 4194304, 'description' => 'Market transaction journal of character.'));
        EveApiCalllist::create(array('type' => 'Character', 'name' => 'WalletJournal', 'accessMask' => 2097152, 'description' => 'Wallet journal of character.'));
        EveApiCalllist::create(array('type' => 'Character', 'name' => 'UpcomingCalendarEvents', 'accessMask' => 1048576, 'description' => 'Upcoming events on characters calendar.'));
        EveApiCalllist::create(array('type' => 'Character', 'name' => 'Standings', 'accessMask' => 524288, 'description' => 'NPC Standings towards the character.'));
        EveApiCalllist::create(array('type' => 'Character', 'name' => 'SkillQueue', 'accessMask' => 262144, 'description' => 'Entire skill queue of character.'));
        EveApiCalllist::create(array('type' => 'Character', 'name' => 'SkillInTraining', 'accessMask' => 131072, 'description' => 'Skill currently in training on the character. Subset of entire Skill Queue.'));
        EveApiCalllist::create(array('type' => 'Character', 'name' => 'Research', 'accessMask' => 65536, 'description' => 'List of all Research agents working for the character and the progress of the research.'));
        EveApiCalllist::create(array('type' => 'Character', 'name' => 'NotificationTexts', 'accessMask' => 32768, 'description' => 'Actual body of notifications sent to the character. Requires Notification access to function.'));
        EveApiCalllist::create(array('type' => 'Character', 'name' => 'Notifications', 'accessMask' => 16384, 'description' => 'List of recent notifications sent to the character.'));
        EveApiCalllist::create(array('type' => 'Character', 'name' => 'Medals', 'accessMask' => 8192, 'description' => 'Medals awarded to the character.'));
        EveApiCalllist::create(array('type' => 'Character', 'name' => 'MarketOrders', 'accessMask' => 4096, 'description' => 'List of all Market Orders the character has made.'));
        EveApiCalllist::create(array('type' => 'Character', 'name' => 'MailMessages', 'accessMask' => 2048, 'description' => 'List of all messages in the characters EVE Mail Inbox.'));
        EveApiCalllist::create(array('type' => 'Character', 'name' => 'MailingLists', 'accessMask' => 1024, 'description' => 'List of all Mailing Lists the character subscribes to.'));
        EveApiCalllist::create(array('type' => 'Character', 'name' => 'MailBodies', 'accessMask' => 512, 'description' => 'EVE Mail bodies. Requires MailMessages as well to function.'));
        EveApiCalllist::create(array('type' => 'Character', 'name' => 'KillLog', 'accessMask' => 256, 'description' => 'Characters kill log.'));
        EveApiCalllist::create(array('type' => 'Character', 'name' => 'IndustryJobs', 'accessMask' => 128, 'description' => 'Character jobs, completed and active.'));
        EveApiCalllist::create(array('type' => 'Character', 'name' => 'FacWarStats', 'accessMask' => 64, 'description' => 'Characters Factional Warfare Statistics.'));
        EveApiCalllist::create(array('type' => 'Character', 'name' => 'ContactNotifications', 'accessMask' => 32, 'description' => 'Most recent contact notifications for the character.'));
        EveApiCalllist::create(array('type' => 'Character', 'name' => 'ContactList', 'accessMask' => 16, 'description' => 'List of character contacts and relationship levels.'));
        EveApiCalllist::create(array('type' => 'Character', 'name' => 'CharacterSheet', 'accessMask' => 8, 'description' => 'Character Sheet information. Contains basic \'Show Info\' information along with clones, account balance, implants, attributes, skills, certificates and corporation roles.'));
        EveApiCalllist::create(array('type' => 'Character', 'name' => 'CalendarEventAttendees', 'accessMask' => 4, 'description' => 'Event attendee responses. Requires UpcomingCalendarEvents to function.'));
        EveApiCalllist::create(array('type' => 'Character', 'name' => 'AssetList', 'accessMask' => 2, 'description' => 'Entire asset list of character.'));
        EveApiCalllist::create(array('type' => 'Character', 'name' => 'AccountBalance', 'accessMask' => 1, 'description' => 'Current balance of characters wallet.'));
        EveApiCalllist::create(array('type' => 'Corporation', 'name' => 'MemberTrackingExtended', 'accessMask' => 33554432, 'description' => 'Extensive Member information. Time of last logoff, last known location and ship.'));
        EveApiCalllist::create(array('type' => 'Corporation', 'name' => 'Locations', 'accessMask' => 16777216, 'description' => 'Allows the fetching of coordinate and name data for items owned by the corporation.'));
        EveApiCalllist::create(array('type' => 'Corporation', 'name' => 'Contracts', 'accessMask' => 8388608, 'description' => 'List of recent Contracts the corporation is involved in.'));
        EveApiCalllist::create(array('type' => 'Corporation', 'name' => 'Titles', 'accessMask' => 4194304, 'description' => 'Titles of corporation and the roles they grant.'));
        EveApiCalllist::create(array('type' => 'Corporation', 'name' => 'WalletTransactions', 'accessMask' => 2097152, 'description' => 'Market transactions of all corporate accounts.'));
        EveApiCalllist::create(array('type' => 'Corporation', 'name' => 'WalletJournal', 'accessMask' => 1048576, 'description' => 'Wallet journal for all corporate accounts.'));
        EveApiCalllist::create(array('type' => 'Corporation', 'name' => 'StarbaseList', 'accessMask' => 524288, 'description' => 'List of all corporate starbases.'));
        EveApiCalllist::create(array('type' => 'Corporation', 'name' => 'Standings', 'accessMask' => 262144, 'description' => 'NPC Standings towards corporation.'));
        EveApiCalllist::create(array('type' => 'Corporation', 'name' => 'StarbaseDetail', 'accessMask' => 131072, 'description' => 'List of all settings of corporate starbases.'));
        EveApiCalllist::create(array('type' => 'Corporation', 'name' => 'Shareholders', 'accessMask' => 65536, 'description' => 'Shareholders of the corporation.'));
        EveApiCalllist::create(array('type' => 'Corporation', 'name' => 'OutpostServiceDetail', 'accessMask' => 32768, 'description' => 'List of all service settings of corporate outposts.'));
        EveApiCalllist::create(array('type' => 'Corporation', 'name' => 'OutpostList', 'accessMask' => 16384, 'description' => 'List of all outposts controlled by the corporation.'));
        EveApiCalllist::create(array('type' => 'Corporation', 'name' => 'Medals', 'accessMask' => 8192, 'description' => 'List of all medals created by the corporation.'));
        EveApiCalllist::create(array('type' => 'Corporation', 'name' => 'MarketOrders', 'accessMask' => 4096, 'description' => 'List of all corporate market orders.'));
        EveApiCalllist::create(array('type' => 'Corporation', 'name' => 'MemberTrackingLimited', 'accessMask' => 2048, 'description' => 'Limited Member information.'));
        EveApiCalllist::create(array('type' => 'Corporation', 'name' => 'MemberSecurityLog', 'accessMask' => 1024, 'description' => 'Member role and title change log.'));
        EveApiCalllist::create(array('type' => 'Corporation', 'name' => 'MemberSecurity', 'accessMask' => 512, 'description' => 'Member roles and titles.'));
        EveApiCalllist::create(array('type' => 'Corporation', 'name' => 'KillLog', 'accessMask' => 256, 'description' => 'Corporation kill log.'));
        EveApiCalllist::create(array('type' => 'Corporation', 'name' => 'IndustryJobs', 'accessMask' => 128, 'description' => 'Corporation jobs, completed and active.'));
        EveApiCalllist::create(array('type' => 'Corporation', 'name' => 'FacWarStats', 'accessMask' => 64, 'description' => 'Corporations Factional Warfare Statistics.'));
        EveApiCalllist::create(array('type' => 'Corporation', 'name' => 'ContainerLog', 'accessMask' => 32, 'description' => 'Corporate secure container acess log.'));
        EveApiCalllist::create(array('type' => 'Corporation', 'name' => 'ContactList', 'accessMask' => 16, 'description' => 'Corporate contact list and relationships.'));
        EveApiCalllist::create(array('type' => 'Corporation', 'name' => 'CorporationSheet', 'accessMask' => 8, 'description' => 'Exposes basic \'Show Info\' information as well as Member Limit and basic division and wallet info.'));
        EveApiCalllist::create(array('type' => 'Corporation', 'name' => 'MemberMedals', 'accessMask' => 4, 'description' => 'List of medals awarded to corporation members.'));
        EveApiCalllist::create(array('type' => 'Corporation', 'name' => 'AssetList', 'accessMask' => 2, 'description' => 'List of all corporation assets.'));
        EveApiCalllist::create(array('type' => 'Corporation', 'name' => 'AccountBalance', 'accessMask' => 1, 'description' => 'Current balance of all corporation accounts.'));

        // New PI Call Lists - Not in calllist.xml.aspx at time of seed
        EveApiCalllist::create(array('type' => 'Character', 'name' => 'PlanetaryColonies', 'accessMask' => 2, 'description' => ''));
        EveApiCalllist::create(array('type' => 'Character', 'name' => 'PlanetaryPins', 'accessMask' => 2, 'description' => ''));
        EveApiCalllist::create(array('type' => 'Character', 'name' => 'PlanetaryRoutes', 'accessMask' => 2, 'description' => ''));
        EveApiCalllist::create(array('type' => 'Character', 'name' => 'PlanetaryLinks', 'accessMask' => 2, 'description' => ''));
    }
}
