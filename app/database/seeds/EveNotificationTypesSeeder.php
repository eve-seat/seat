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

class EveNotificationTypesSeeder extends Seeder
{

    public function run()
    {
        DB::table('eve_notification_types')->delete();

        EveEveNotificationTypes::create(array("typeID" => "1", "description" => "Legacy"));
        EveEveNotificationTypes::create(array("typeID" => "2", "description" => "Character deleted"));
        EveEveNotificationTypes::create(array("typeID" => "3", "description" => "Give medal to character"));
        EveEveNotificationTypes::create(array("typeID" => "4", "description" => "Alliance maintenance bill"));
        EveEveNotificationTypes::create(array("typeID" => "5", "description" => "Alliance war declared"));
        EveEveNotificationTypes::create(array("typeID" => "6", "description" => "Alliance war surrender"));
        EveEveNotificationTypes::create(array("typeID" => "7", "description" => "Alliance war retracted"));
        EveEveNotificationTypes::create(array("typeID" => "8", "description" => "Alliance war invalidated by Concord"));
        EveEveNotificationTypes::create(array("typeID" => "9", "description" => "Bill issued to a character"));
        EveEveNotificationTypes::create(array("typeID" => "10", "description" => "Bill issued to corporation or alliance"));
        EveEveNotificationTypes::create(array("typeID" => "11", "description" => "Bill not paid because there's not enough ISK available"));
        EveEveNotificationTypes::create(array("typeID" => "12", "description" => "Bill"));
        EveEveNotificationTypes::create(array("typeID" => "13", "description" => "Bill"));
        EveEveNotificationTypes::create(array("typeID" => "14", "description" => "Bounty claimed"));
        EveEveNotificationTypes::create(array("typeID" => "15", "description" => "Clone activated"));
        EveEveNotificationTypes::create(array("typeID" => "16", "description" => "New corp member application"));
        EveEveNotificationTypes::create(array("typeID" => "17", "description" => "Corp application rejected"));
        EveEveNotificationTypes::create(array("typeID" => "18", "description" => "Corp application accepted"));
        EveEveNotificationTypes::create(array("typeID" => "19", "description" => "Corp tax rate changed"));
        EveEveNotificationTypes::create(array("typeID" => "20", "description" => "Corp news report"));
        EveEveNotificationTypes::create(array("typeID" => "21", "description" => "Player leaves corp"));
        EveEveNotificationTypes::create(array("typeID" => "22", "description" => "Corp news"));
        EveEveNotificationTypes::create(array("typeID" => "23", "description" => "Corp dividend/liquidation"));
        EveEveNotificationTypes::create(array("typeID" => "24", "description" => "Corp dividend payout"));
        EveEveNotificationTypes::create(array("typeID" => "25", "description" => "Corp vote created"));
        EveEveNotificationTypes::create(array("typeID" => "26", "description" => "Corp CEO votes revoked during voting"));
        EveEveNotificationTypes::create(array("typeID" => "27", "description" => "Corp declares war"));
        EveEveNotificationTypes::create(array("typeID" => "28", "description" => "Corp war has started"));
        EveEveNotificationTypes::create(array("typeID" => "29", "description" => "Corp surrenders war"));
        EveEveNotificationTypes::create(array("typeID" => "30", "description" => "Corp retracts war"));
        EveEveNotificationTypes::create(array("typeID" => "31", "description" => "Corp war invalidated by Concord"));
        EveEveNotificationTypes::create(array("typeID" => "32", "description" => "Container password retrieval"));
        EveEveNotificationTypes::create(array("typeID" => "33", "description" => "Contraband or low standings cause an attack or items being confiscated"));
        EveEveNotificationTypes::create(array("typeID" => "34", "description" => "First ship insurance"));
        EveEveNotificationTypes::create(array("typeID" => "35", "description" => "Ship destroyed"));
        EveEveNotificationTypes::create(array("typeID" => "36", "description" => "Insurance contract invalidated/runs out"));
        EveEveNotificationTypes::create(array("typeID" => "37", "description" => "Sovereignty claim fails (alliance)"));
        EveEveNotificationTypes::create(array("typeID" => "38", "description" => "Sovereignty claim fails (corporation)"));
        EveEveNotificationTypes::create(array("typeID" => "39", "description" => "Sovereignty bill late (alliance)"));
        EveEveNotificationTypes::create(array("typeID" => "40", "description" => "Sovereignty bill late (corporation)"));
        EveEveNotificationTypes::create(array("typeID" => "41", "description" => "Sovereignty claim lost (alliance)"));
        EveEveNotificationTypes::create(array("typeID" => "42", "description" => "Sovereignty claim lost (corporation)"));
        EveEveNotificationTypes::create(array("typeID" => "43", "description" => "Sovereignty claim acquired (alliance)"));
        EveEveNotificationTypes::create(array("typeID" => "44", "description" => "Sovereignty claim acquired (corporation)"));
        EveEveNotificationTypes::create(array("typeID" => "45", "description" => "Alliance anchoring alert"));
        EveEveNotificationTypes::create(array("typeID" => "46", "description" => "Alliance structure turns vulnerable"));
        EveEveNotificationTypes::create(array("typeID" => "47", "description" => "Alliance structure turns invulnerable"));
        EveEveNotificationTypes::create(array("typeID" => "48", "description" => "Sovereignty disruptor anchored"));
        EveEveNotificationTypes::create(array("typeID" => "49", "description" => "Structure won/lost"));
        EveEveNotificationTypes::create(array("typeID" => "50", "description" => "Corp office lease expiration notice"));
        EveEveNotificationTypes::create(array("typeID" => "51", "description" => "Clone contract revoked by station manager"));
        EveEveNotificationTypes::create(array("typeID" => "52", "description" => "Corp member clones moved between stations"));
        EveEveNotificationTypes::create(array("typeID" => "53", "description" => "Clone contract revoked by station manager"));
        EveEveNotificationTypes::create(array("typeID" => "54", "description" => "Insurance contract expired"));
        EveEveNotificationTypes::create(array("typeID" => "55", "description" => "Insurance contract issued"));
        EveEveNotificationTypes::create(array("typeID" => "56", "description" => "Jump clone destroyed"));
        EveEveNotificationTypes::create(array("typeID" => "57", "description" => "Jump clone destroyed"));
        EveEveNotificationTypes::create(array("typeID" => "58", "description" => "Corporation joining factional warfare"));
        EveEveNotificationTypes::create(array("typeID" => "59", "description" => "Corporation leaving factional warfare"));
        EveEveNotificationTypes::create(array("typeID" => "60", "description" => "Corporation kicked from factional warfare on startup because of too low standing to the faction"));
        EveEveNotificationTypes::create(array("typeID" => "61", "description" => "Character kicked from factional warfare on startup because of too low standing to the faction"));
        EveEveNotificationTypes::create(array("typeID" => "62", "description" => "Corporation in factional warfare warned on startup because of too low standing to the faction"));
        EveEveNotificationTypes::create(array("typeID" => "63", "description" => "Character in factional warfare warned on startup because of too low standing to the faction"));
        EveEveNotificationTypes::create(array("typeID" => "64", "description" => "Character loses factional warfare rank"));
        EveEveNotificationTypes::create(array("typeID" => "65", "description" => "Character gains factional warfare rank"));
        EveEveNotificationTypes::create(array("typeID" => "66", "description" => "Agent has moved"));
        EveEveNotificationTypes::create(array("typeID" => "67", "description" => "Mass transaction reversal message"));
        EveEveNotificationTypes::create(array("typeID" => "68", "description" => "Reimbursement message"));
        EveEveNotificationTypes::create(array("typeID" => "69", "description" => "Agent locates a character"));
        EveEveNotificationTypes::create(array("typeID" => "70", "description" => "Research mission becomes available from an agent"));
        EveEveNotificationTypes::create(array("typeID" => "71", "description" => "Agent mission offer expires"));
        EveEveNotificationTypes::create(array("typeID" => "72", "description" => "Agent mission times out"));
        EveEveNotificationTypes::create(array("typeID" => "73", "description" => "Agent offers a storyline mission"));
        EveEveNotificationTypes::create(array("typeID" => "74", "description" => "Tutorial message sent on character creation"));
        EveEveNotificationTypes::create(array("typeID" => "75", "description" => "Tower alert"));
        EveEveNotificationTypes::create(array("typeID" => "76", "description" => "Tower resource alert"));
        EveEveNotificationTypes::create(array("typeID" => "77", "description" => "Station service aggression message"));
        EveEveNotificationTypes::create(array("typeID" => "78", "description" => "Station state change message"));
        EveEveNotificationTypes::create(array("typeID" => "79", "description" => "Station conquered message"));
        EveEveNotificationTypes::create(array("typeID" => "80", "description" => "Station aggression message"));
        EveEveNotificationTypes::create(array("typeID" => "81", "description" => "Corporation requests joining factional warfare"));
        EveEveNotificationTypes::create(array("typeID" => "82", "description" => "Corporation requests leaving factional warfare"));
        EveEveNotificationTypes::create(array("typeID" => "83", "description" => "Corporation withdrawing a request to join factional warfare"));
        EveEveNotificationTypes::create(array("typeID" => "84", "description" => "Corporation withdrawing a request to leave factional warfare"));
        EveEveNotificationTypes::create(array("typeID" => "85", "description" => "Corporation liquidation"));
        EveEveNotificationTypes::create(array("typeID" => "86", "description" => "Territorial Claim Unit under attack"));
        EveEveNotificationTypes::create(array("typeID" => "87", "description" => "Sovereignty Blockade Unit under attack"));
        EveEveNotificationTypes::create(array("typeID" => "88", "description" => "Infrastructure Hub under attack"));
        EveEveNotificationTypes::create(array("typeID" => "89", "description" => "Contact add notification"));
        EveEveNotificationTypes::create(array("typeID" => "90", "description" => "Contact edit notification"));
        EveEveNotificationTypes::create(array("typeID" => "91", "description" => "Incursion Completed"));
        EveEveNotificationTypes::create(array("typeID" => "92", "description" => "Corp Kicked"));
        EveEveNotificationTypes::create(array("typeID" => "93", "description" => "Customs office has been attacked"));
        EveEveNotificationTypes::create(array("typeID" => "94", "description" => "Customs office has entered reinforced"));
        EveEveNotificationTypes::create(array("typeID" => "95", "description" => "Customs office has been transferred"));
        EveEveNotificationTypes::create(array("typeID" => "96", "description" => "FW Alliance Warning"));
        EveEveNotificationTypes::create(array("typeID" => "97", "description" => "FW Alliance Kick"));
        EveEveNotificationTypes::create(array("typeID" => "98", "description" => "AllWarCorpJoined Msg"));
        EveEveNotificationTypes::create(array("typeID" => "99", "description" => "Ally Joined Defender"));
        EveEveNotificationTypes::create(array("typeID" => "100", "description" => "Ally Has Joined a War Aggressor"));
        EveEveNotificationTypes::create(array("typeID" => "101", "description" => "Ally Joined War Ally"));
        EveEveNotificationTypes::create(array("typeID" => "102", "description" => "New war system: entity is offering assistance in a war."));
        EveEveNotificationTypes::create(array("typeID" => "103", "description" => "War Surrender Offer"));
        EveEveNotificationTypes::create(array("typeID" => "104", "description" => "War Surrender Declined"));
        EveEveNotificationTypes::create(array("typeID" => "105", "description" => "FacWar LP Payout Kill"));
        EveEveNotificationTypes::create(array("typeID" => "106", "description" => "FacWar LP Payout Event"));
        EveEveNotificationTypes::create(array("typeID" => "107", "description" => "FacWar LP Disqualified Eventd"));
        EveEveNotificationTypes::create(array("typeID" => "108", "description" => "FacWar LP Disqualified Kill"));
        EveEveNotificationTypes::create(array("typeID" => "109", "description" => "Alliance Contract Cancelled"));
        EveEveNotificationTypes::create(array("typeID" => "110", "description" => "War Ally Declined Offer"));
        EveEveNotificationTypes::create(array("typeID" => "111", "description" => "Your Bounty Claimed"));
        EveEveNotificationTypes::create(array("typeID" => "112", "description" => "Bounty Placed (Char)"));
        EveEveNotificationTypes::create(array("typeID" => "113", "description" => "Bounty Placed (Corp)"));
        EveEveNotificationTypes::create(array("typeID" => "114", "description" => "Bounty Placed (Alliance)"));
        EveEveNotificationTypes::create(array("typeID" => "115", "description" => "Kill Right Available"));
        EveEveNotificationTypes::create(array("typeID" => "116", "description" => "Kill Right Available Open"));
        EveEveNotificationTypes::create(array("typeID" => "117", "description" => "Kill Right Earned"));
        EveEveNotificationTypes::create(array("typeID" => "118", "description" => "Kill Right Used"));
        EveEveNotificationTypes::create(array("typeID" => "119", "description" => "Kill Right Unavailable"));
        EveEveNotificationTypes::create(array("typeID" => "120", "description" => "Kill Right Unavailable Open"));
        EveEveNotificationTypes::create(array("typeID" => "121", "description" => "Declare War"));
        EveEveNotificationTypes::create(array("typeID" => "122", "description" => "Offered Surrender"));
        EveEveNotificationTypes::create(array("typeID" => "123", "description" => "Accepted Surrender"));
        EveEveNotificationTypes::create(array("typeID" => "124", "description" => "Made War Mutual"));
        EveEveNotificationTypes::create(array("typeID" => "125", "description" => "Retracts War"));
        EveEveNotificationTypes::create(array("typeID" => "126", "description" => "Offered To Ally"));
        EveEveNotificationTypes::create(array("typeID" => "127", "description" => "Accepted Ally"));
        EveEveNotificationTypes::create(array("typeID" => "128", "description" => "Character Application Accept Message"));
        EveEveNotificationTypes::create(array("typeID" => "129", "description" => "Character Application Reject Message"));
        EveEveNotificationTypes::create(array("typeID" => "130", "description" => "Character Application Withdraw Message"));
    }

}
