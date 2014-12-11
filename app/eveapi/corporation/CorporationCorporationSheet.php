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

namespace Seat\EveApi\Corporation;

use Seat\EveApi\BaseApi;
use Pheal\Pheal;

class CorporationSheet extends BaseApi
{

    public static function Update($keyID, $vCode)
    {

        // Start and validate they key pair
        BaseApi::bootstrap();
        BaseApi::validateKeyPair($keyID, $vCode);

        // Set key scopes and check if the call is banned
        $scope = 'Corp';
        $api = 'CorporationSheet';

        if (BaseApi::isBannedCall($api, $scope, $keyID))
            return;

        // Get the characters for this key
        $characters = BaseApi::findKeyCharacters($keyID);

        // Check if this key has any characters associated with it
        if (!$characters)
            return;

        // Lock the call so that we are the only instance of this running now()
        // If it is already locked, just return without doing anything
        if (!BaseApi::isLockedCall($api, $scope, $keyID))
            $lockhash = BaseApi::lockCall($api, $scope, $keyID);
        else
            return;

        // So I think a corporation key will only ever have one character
        // attached to it. So we will just use that characterID for auth
        // things, but the corporationID for locking etc.
        $corporationID = BaseApi::findCharacterCorporation($characters[0]);

        // Prepare the Pheal instance
        $pheal = new Pheal($keyID, $vCode);

        // Do the actual API call. pheal-ng actually handles some internal
        // caching too.
        try {

            $corporation_sheet = $pheal
                ->corpScope
                ->CorporationSheet(array('characterID' => $characters[0]));

        } catch (\Pheal\Exceptions\APIException $e) {

            // If we cant get account status information, prevent us from calling
            // this API again
            BaseApi::banCall($api, $scope, $keyID, 0, $e->getCode() . ': ' . $e->getMessage());
            return;

        } catch (\Pheal\Exceptions\PhealException $e) {

            throw $e;
        }

        // Check if the data in the database is still considered up to date.
        // checkDbCache will return true if this is the case
        if (!BaseApi::checkDbCache($scope, $api, $corporation_sheet->cached_until, $corporationID)) {

            $corporation_data = \EveCorporationCorporationSheet::where('corporationID', '=', $corporationID)->first();

            if (!$corporation_data)
                $corporation_data = new \EveCorporationCorporationSheet;

            $corporation_data->corporationID = $corporation_sheet->corporationID;
            $corporation_data->corporationName = $corporation_sheet->corporationName;
            $corporation_data->ticker = $corporation_sheet->ticker;
            $corporation_data->ceoID = $corporation_sheet->ceoID;
            $corporation_data->ceoName = $corporation_sheet->ceoName;
            $corporation_data->stationID = $corporation_sheet->stationID;
            $corporation_data->stationName = $corporation_sheet->stationName;
            $corporation_data->description = $corporation_sheet->description;
            $corporation_data->url = $corporation_sheet->url;
            $corporation_data->allianceID = $corporation_sheet->allianceID;
            $corporation_data->factionID = $corporation_sheet->factionID;
            $corporation_data->allianceName = $corporation_sheet->allianceName;
            $corporation_data->taxRate = $corporation_sheet->taxRate;
            $corporation_data->memberCount = $corporation_sheet->memberCount;
            $corporation_data->memberLimit = $corporation_sheet->memberLimit;
            $corporation_data->shares = $corporation_sheet->shares;
            $corporation_data->graphicID = $corporation_sheet->logo->graphicID;
            $corporation_data->shape1 = $corporation_sheet->logo->shape1;
            $corporation_data->shape2 = $corporation_sheet->logo->shape2;
            $corporation_data->shape3 = $corporation_sheet->logo->shape3;
            $corporation_data->color1 = $corporation_sheet->logo->color1;
            $corporation_data->color2 = $corporation_sheet->logo->color2;
            $corporation_data->color3 = $corporation_sheet->logo->color3;
            $corporation_data->corporationID = $corporation_sheet->corporationID;
            $corporation_data->save();

            // Update the Divisions
            foreach ($corporation_sheet->divisions as $division) {

                $division_data = \EveCorporationCorporationSheetDivisions::where('corporationID', '=', $corporationID)
                    ->where('accountKey', '=', $division->accountKey)
                    ->first();

                if (!$division_data)
                    $division_data = new \EveCorporationCorporationSheetDivisions;

                $division_data->corporationID = $corporationID;
                $division_data->accountKey = $division->accountKey;
                $division_data->description = $division->description;
                $corporation_data->divisions()->save($division_data);
            }

            // Update the Wallet Divisions
            foreach ($corporation_sheet->walletDivisions as $division) {

                $division_data = \EveCorporationCorporationSheetWalletDivisions::where('corporationID', '=', $corporationID)
                    ->where('accountKey', '=', $division->accountKey)
                    ->first();

                if (!$division_data)
                    $division_data = new \EveCorporationCorporationSheetWalletDivisions;

                $division_data->corporationID = $corporationID;
                $division_data->accountKey = $division->accountKey;
                $division_data->description = $division->description;
                $corporation_data->walletdivisions()->save($division_data);
            }

            // Update the cached_until time in the database for this api call
            BaseApi::setDbCache($scope, $api, $corporation_sheet->cached_until, $corporationID);
        }

        // Unlock the call
        BaseApi::unlockCall($lockhash);

        return $corporation_sheet;
    }
}
