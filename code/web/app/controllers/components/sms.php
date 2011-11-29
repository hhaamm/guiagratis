<?php
/*
 * Guia Gratis, sistema para intercambio de regalos.
 * Copyright (C) 2011  Hugo Alberto Massaroli
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
class SmsComponent extends Object {
    function sendVerificationCode($code, $number) {
        return $this->send("This is your verification code: $code", $number);
    }

    function send($message, $number) {
        // include the PHP TwilioRest library
        App::import('Vendor','Twilio');

        // twilio REST API version
        $ApiVersion = "2008-08-01";
        
        // set our AccountSid and AuthToken
        $AccountSid = Configure::read('Twilio.AccountSID');
        $AuthToken = Configure::read('Twilio.AuthToken');

        // instantiate a new Twilio Rest Client
        $client = new TwilioRestClient($AccountSid, $AuthToken);
        // Send a new outgoinging SMS by POST'ing to the SMS resource */
        $response = $client->request("/$ApiVersion/Accounts/$AccountSid/SMS/Messages",
                "POST", array(
                "To" => $number,
                "From" => Configure::read('Twilio.FromNumber'),
                "Body" => $message
        ));
        if($response->IsError) {
            debug($response->ErrorMessage);
            return false;
        } else
            return true;

    }
}
?>