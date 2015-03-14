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

class SeatPermissionsSeeder extends Seeder
{

    public function run()
    {
        // Temporarily disable mass assignment restrictions
        Eloquent::unguard();

        DB::table('seat_permissions')->truncate();

        SeatPermissions::create(array('permission' => 'superuser', 'description' => 'Full Administrator'));

        SeatPermissions::create(array('permission' => 'api_key_delete', 'description' => 'Delete EVE API Keys'));
        SeatPermissions::create(array('permission' => 'api_key_detail', 'description' => 'View EVE API Keys'));
        SeatPermissions::create(array('permission' => 'api_key_enable_disable', 'description' => 'Enable / Disable EVE API Keys'));
        SeatPermissions::create(array('permission' => 'api_key_list', 'description' => 'List all EVE API Keys'));
        SeatPermissions::create(array('permission' => 'api_key_mass_enable', 'description' => 'Enable all keys and remove all bans'));
        SeatPermissions::create(array('permission' => 'api_key_update', 'description' => 'Modify EVE API Keys'));
        SeatPermissions::create(array('permission' => 'character_assets', 'description' => 'View all characters assets'));
        SeatPermissions::create(array('permission' => 'character_calendar', 'description' => 'View all characters calendars'));
        SeatPermissions::create(array('permission' => 'character_contacts', 'description' => 'View all characters contacts'));
        SeatPermissions::create(array('permission' => 'character_contracts', 'description' => 'View all characters contracts'));
        SeatPermissions::create(array('permission' => 'character_industry', 'description' => 'View all characters industry'));
        SeatPermissions::create(array('permission' => 'character_killmails', 'description' => 'View all characters killmails'));
        SeatPermissions::create(array('permission' => 'character_list_all', 'description' => 'List all characters'));
        SeatPermissions::create(array('permission' => 'character_mail', 'description' => 'View all characters mail'));
        SeatPermissions::create(array('permission' => 'character_market_orders', 'description' => 'View all characters market orders'));
        SeatPermissions::create(array('permission' => 'character_notifications', 'description' => 'View all characters notifications'));
        SeatPermissions::create(array('permission' => 'character_pi', 'description' => 'View all characters planetary interaction'));
        SeatPermissions::create(array('permission' => 'character_research_agents', 'description' => 'View all characters research agents'));
        SeatPermissions::create(array('permission' => 'character_skills', 'description' => 'View all characters skills'));
        SeatPermissions::create(array('permission' => 'character_standings', 'description' => 'View all characters standings'));
        SeatPermissions::create(array('permission' => 'character_view_summary', 'description' => 'View all characters summaries'));
        SeatPermissions::create(array('permission' => 'character_wallet_journal', 'description' => 'View all characters wallet journals'));
        SeatPermissions::create(array('permission' => 'character_wallet_transactions', 'description' => 'View all characters wallet transactions'));
        SeatPermissions::create(array('permission' => 'corporation_assets', 'description' => 'View all characters assets'));
        SeatPermissions::create(array('permission' => 'corporation_contracts', 'description' => 'View all corporations contracts'));
        SeatPermissions::create(array('permission' => 'corporation_industry', 'description' => 'View all corporations industry'));
        SeatPermissions::create(array('permission' => 'corporation_killmails', 'description' => 'View all corporations killmails'));
        SeatPermissions::create(array('permission' => 'corporation_ledger', 'description' => 'View all corporations ledgers'));
        SeatPermissions::create(array('permission' => 'corporation_list_all', 'description' => 'List all corporations'));
        SeatPermissions::create(array('permission' => 'corporation_market_orders', 'description' => 'View all corporations market orders'));
        SeatPermissions::create(array('permission' => 'corporation_member_security', 'description' => 'View all corporations member security'));
        SeatPermissions::create(array('permission' => 'corporation_member_standings', 'description' => 'View all corporations member standings'));
        SeatPermissions::create(array('permission' => 'corporation_member_tracking', 'description' => 'View all corporations member tracking'));
        SeatPermissions::create(array('permission' => 'corporation_poco', 'description' => 'View all corporations customs offices'));
        SeatPermissions::create(array('permission' => 'corporation_star_bases', 'description' => 'View all corporations star bases'));
        SeatPermissions::create(array('permission' => 'corporation_wallet_journal', 'description' => 'View all corporations wallet journals'));
        SeatPermissions::create(array('permission' => 'corporation_wallet_transactions', 'description' => 'View all corporations wallet transactions'));
        SeatPermissions::create(array('permission' => 'mail_all_bodies', 'description' => 'View all mail bodies'));
        SeatPermissions::create(array('permission' => 'mail_all_subjects', 'description' => 'View all mail subjects'));
        SeatPermissions::create(array('permission' => 'people_groups_create', 'description' => 'Create people groups'));
        SeatPermissions::create(array('permission' => 'people_groups_edit', 'description' => 'Edit people groups'));
        SeatPermissions::create(array('permission' => 'people_groups_view_all', 'description' => 'View all people groups'));
        SeatPermissions::create(array('permission' => 'search_all_character_assets', 'description' => 'Search all character assets'));
        SeatPermissions::create(array('permission' => 'search_all_character_contact_lists', 'description' => 'Search all character contact lists'));
        SeatPermissions::create(array('permission' => 'search_all_character_mail', 'description' => 'Search all character mail'));
        SeatPermissions::create(array('permission' => 'search_all_characters', 'description' => 'Search all characters'));
        SeatPermissions::create(array('permission' => 'search_all_character_skills', 'description' => 'Search all character skills'));
        SeatPermissions::create(array('permission' => 'search_all_character_standings', 'description' => 'Search all character standings'));
        SeatPermissions::create(array('permission' => 'search_all_corporation_assets', 'description' => 'Search all corporation assests'));
        SeatPermissions::create(array('permission' => 'search_all_corporation_standings', 'description' => 'Search all corporation standings'));


    }
}
