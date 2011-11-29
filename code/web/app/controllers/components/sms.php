<?php
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