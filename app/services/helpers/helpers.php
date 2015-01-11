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

namespace App\Services\Helpers;

class Helpers
{

    /*
    |--------------------------------------------------------------------------
    | findSkillLevel()
    |--------------------------------------------------------------------------
    |
    | Take a the characters skills array and search for the level at which
    | a certain skillID is.
    |
    */
    public static function getCorporationList()
    {
        $corporations = \DB::table('account_apikeyinfo')
            ->join('account_apikeyinfo_characters', 'account_apikeyinfo.keyID', '=', 'account_apikeyinfo_characters.keyID')
            ->where('account_apikeyinfo.type', 'Corporation');

        if (!\Auth::isSuperUser() )
            $corporations = $corporations->whereIn('corporationID', \Session::get('corporation_affiliations'))->get();
        else
            $corporations = $corporations->get();

        return $corporations;
    }

    /*
    |--------------------------------------------------------------------------
    | findSkillLevel()
    |--------------------------------------------------------------------------
    |
    | Take a the characters skills array and search for the level at which
    | a certain skillID is.
    |
    */

    public static function findSkillLevel($character_skills, $skillID) {

        foreach ($character_skills as $skill_group) {

            foreach ($skill_group as $skill) {

                if ($skill['typeID'] == $skillID)
                    return $skill['level'];
            }
        }

        // If we can not find the skill, then it is 0
        return 0;
    }

    /*
    |--------------------------------------------------------------------------
    | processAccessMask()
    |--------------------------------------------------------------------------
    |
    | Run through the api calllist, checking which calls a accessmask has
    | access to
    |
    */

    public static function processAccessMask($mask, $type) {

        // Prepare the return array
        $return = array();

        // Loop over the call list, populating the array, based on the type
        if ($type == 'Corporation') {

            // Loop over the call list, populating the array
            foreach (\EveApiCalllist::where('type', 'Corporation')->get() as $call)
                $return[$call->name] = (int)$call->accessMask & (int)$mask ? 'true' : 'false';

        } else {

            // Loop over the call list, populating the array
            foreach (\EveApiCalllist::where('type', 'Character')->get() as $call)
                $return[$call->name] = (int)$call->accessMask & (int)$mask ? 'true' : 'false';
        }

        // Return the populated array
        return $return;
    }

    /*
    |--------------------------------------------------------------------------
    | formatBigNumber()
    |--------------------------------------------------------------------------
    |
    | Format a number to condesed format with suffix
    |
    */

    public static function formatBigNumber($number) {

        // first strip any formatting;
        $number = (0 + str_replace(',', '', $number));

        // is this a number?
        if(!is_numeric($number))
            return false;

        $places = 0;

        // now filter it;
        if($number > 1000000000000)
            return round(($number / 1000000000000), 1) . 't';

        else if($number > 1000000000)
            return round(($number / 1000000000), 1) . 'b';

        else if($number > 1000000)
            return round(($number / 1000000), 1) . 'm';

        else if($number > 1000)
            return round(($number / 1000), 1) . 'k';

        else if($number > 100)
            $places = 2;
            return $number;

        return Helpers::format_number($number, $places);
    }

    /*
    |--------------------------------------------------------------------------
    | highlightKeyword()
    |--------------------------------------------------------------------------
    |
    | Replaces a word in a string with a highlighted version. Sourced from
    | http://www.webdevdoor.com/php/highlight-search-keyword-string-function/
    |
    */

    public static function highlightKeyword($str, $search) {

        $highlightcolor = "#a94442";
        $occurrences = substr_count(strtolower($str), strtolower($search));
        $newstring = $str;
        $match = array();

        for ($i=0; $i<$occurrences; $i++) {

            $match[$i] = stripos($str, $search, $i);
            $match[$i] = substr($str, $match[$i], strlen($search));
            $newstring = str_replace($match[$i], '[#]'.$match[$i].'[@]', strip_tags($newstring));
        }

        $newstring = str_replace('[#]', '<span style="color: '.$highlightcolor.';">', $newstring);
        $newstring = str_replace('[@]', '</span>', $newstring);
        return $newstring;
    }

    /*
    |--------------------------------------------------------------------------
    | generateEveImage()
    |--------------------------------------------------------------------------
    |
    | Return the URL of image for a given ID and check if it's a character,
    |   corporation or Alliance ID
    | There no way to find if id is character, corporation or alliance before
    |   the 64bits move. For that if the ID given is not in range we consider
    |   it's a character ID.
    | From here: https://forums.eveonline.com/default.aspx?g=posts&m=716708#post716708
    | Valid size is: 32, 64, 128, 256, 512
    | TODO: Find a way to fix the id before 64bits move.
    |
    */

    public static function generateEveImage($id, $size) {

        if($id > 90000000 && $id < 98000000) {

            return '//image.eveonline.com/Character/' . $id . '_' . $size . '.jpg';

        } elseif(($id > 98000000 && $id < 99000000) || ($id > 1000000 && $id < 2000000)) {

            return '//image.eveonline.com/Corporation/' . $id . '_' . $size . '.png';

        } elseif(($id > 99000000 && $id < 100000000) || ($id > 0 && $id < 1000000)) {

            return '//image.eveonline.com/Alliance/' . $id . '_' . $size . '.png';

        } else {

            return '//image.eveonline.com/Character/' . $id . '_' . $size . '.jpg';
        }
    }

    /*
    |--------------------------------------------------------------------------
    | parseCorpSecurityRoleLogs()
    |--------------------------------------------------------------------------
    |
    | Return the Roles from corporation member security-log table
    | TODO: More Documentation.
    |
    */

    public static function parseCorpSecurityRoleLog($role_string) {

        if($role_string == '[]' and is_string($role_string)) {

            return '';

        } elseif ($role_string <> '' and is_string($role_string)) {

            $t = implode(', ',get_object_vars(json_decode($role_string)));
            return str_replace('role','',$t);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | sumVolume()
    |--------------------------------------------------------------------------
    |
    | Returns the total volume of an array of assets
    | param $personal is passed if the assets is calculated for the person
    |   and not the corporation
    |
    */

    public static function sumVolume($array, $col_name, $personal = NULL) {

        $volume = 0;
        foreach($array as $item) {

            if(isset($item['contents'])) {

                foreach($item['contents'] as $type) {

                    if($personal)
                        $volume += ($type[$col_name] * $type['quantity']);
                    else
                        $volume += $type[$col_name];
                }
            }

            if($personal)
                $volume += ($item[$col_name] * $item['quantity']);
            else
                $volume += $item[$col_name];
        }

        return Helpers::formatBigNumber($volume);
    }

    /*
    |--------------------------------------------------------------------------
    | numAssets()
    |--------------------------------------------------------------------------
    |
    | Returns the number of assets including what is within a container
    |
    |
    */

    public static function numAssets($array) {

        $count = 0;

        foreach($array as $item){

            $count++;

            if(isset($item['contents'])) {

                foreach($item['contents'] as $type)
                    $count++;
            }
        }

        return $count;
    }

    /*
    |--------------------------------------------------------------------------
    | marketOrderType()
    |--------------------------------------------------------------------------
    |
    | Returns the total of a market order type, ie:
    | Pass in an array of market orders, with the type (col_name)
    | you are looking for and an integer will be provided for all the
    | orders in that state within the array
    |
    | '0' => 'Active',
    | '1' => 'Closed',
    | '2' => 'Expired / Fulfilled',
    | '3' => 'Cancelled',
    | '4' => 'Pending',
    | '5' => 'Deleted'
    |
    */

    public static function marketOrderCount($array, $col_name) {

        $count = 0;

        foreach($array as $order) {

            if($order->orderState == $col_name)
                $count ++;

        }

        return $count;
    }

    /*
    |--------------------------------------------------------------------------
    | getMembersForTitle()
    |--------------------------------------------------------------------------
    |
    | Returns the members for a certain title
    | pass in corp and title
    |
    */

    public static function getMembersForTitle($corporationID, $titleID)
    {

        $members = \DB::table('corporation_msec_titles')
            ->select('name','characterID')
            ->where('corporationID', $corporationID)
            ->where('titleID', $titleID)
            ->orderBy('name','asc')
            ->remember(0.1)
            ->get('name');

        return $members;
    }



    /*
    |--------------------------------------------------------------------------
    | getSecRolesArray()
    |--------------------------------------------------------------------------
    | 
    | Converts a String or JSON Array of RoleIDs
    | to an Fancy Role Array with Corporation Division's
    |
    | @param mixed_roles: either comma delimited string or JSON serialized array
    | @return: all roles converted to nice looking strings
    |   returns an empty Array if $mixed_roles is empty
    |
    | Caches the DB Responses to reduce DB hits
    |
    */

    public static function getSecRolesArray($mixed_roles, $corporationID)
    {
        
        if($mixed_roles == '' or $mixed_roles == '[]' or is_null($mixed_roles)) {
            return array("None");
        } else {

            /*
            // First try JSON because explode does work also for that
            // if json_decode founds no JSON it returns NULL value
            */
            $roleIDs = (array)json_decode($mixed_roles);
            if($roleIDs==NULL)
                $roleIDs = explode(",",$mixed_roles);

            // Query DB with the RoleIDs
            $role_list= \DB::table('eve_corporation_rolemap')
                ->select('roleID','roleName')
                ->whereIn('roleID', $roleIDs)
                ->orderBy('roleName','asc')
                ->remember(0.1)
                ->get();

            $fancy_roles = Array();

            $divisions_hangar = \DB::table('corporation_corporationsheet_divisions')
                ->select('accountKey','description')
                ->orderBy('accountKey')
                ->where('corporationID',$corporationID)
                ->remember(0.1)
                ->get();
            
            $divisions_wallet = \DB::table('corporation_corporationsheet_walletdivisions')
                ->select('accountKey','description')
                ->orderBy('accountKey')
                ->where('corporationID',$corporationID)
                ->remember(0.1)
                ->get();
            
            foreach ($role_list as $role) {
                $regex = "/(Hangar|Wallet Division|Container from Hangar)\s(\d)/";

                // Regex found no Hangar or Wallet Division -> push roleName into Result
                if(preg_match($regex, $role->roleName, $result)==0) {
                    array_push($fancy_roles, $role->roleName);
                } else {
                    switch ($result[1]) {
                        case 'Hangar':
                            array_push($fancy_roles, str_replace($result[2], $divisions_hangar[$result[2]-1]->description, $role->roleName));
                            break;
                        case 'Wallet Division':
                            array_push($fancy_roles, str_replace($result[2], $divisions_wallet[$result[2]-1]->description, $role->roleName));
                            break;
                        case 'Container from Hangar':
                            array_push($fancy_roles, str_replace($result[2], $divisions_hangar[$result[2]-1]->description, $role->roleName));
                            break;
                        default:
                            array_push($fancy_roles, $role->roleName);
                            break;
                    }
                }
            }
            return  $fancy_roles;
        }
    }
}
