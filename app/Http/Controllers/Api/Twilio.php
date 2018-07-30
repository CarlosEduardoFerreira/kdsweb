<?php

namespace App\Http\Controllers\Api;

require app_path().'/Twilio/autoload.php';

// Use the REST API Client to make requests to the Twilio REST API
use Twilio\Rest\Client;


class ManagerSMS{
    
    private $client;
    
    // Your Account SID and Auth Token from twilio.com/console
    private $sid        = 'AC923c0238ccc0eda2880e148eef1d93ba';
    private $token      = 'b7f4948045a4ded28cb76b2364023501';
    private $phoneFrom  = '16467982984';
    
    // test
//     private $sid        = 'AC6810737c53768016aa22c057ac576c21';
//     private $token      = '8d500259574f86e41ba14cd4db907772';
//     private $phoneFrom  = '16467982984';
    
    
    public function __construct(){
//         $this->client = new Client($this->sid, $this->token);
    }
    
    
    public function configTwilio($sid, $token, $phoneFrom) {
        $this->sid = $sid;
        $this->token = $token;
        $this->phoneFrom = $phoneFrom;
        
        $this->client = new Client($this->sid, $this->token);
    }
    
    
    public function sendSMS($phone, $message){
        return $this->client->messages->create(
            '+'.$phone,
            array(
                'from' => $this->phoneFrom,
                'body' => $message
            )
        );
    }
    
    
    public function validate($phone, $friendlyName){
        $validationRequest = $this->client->validationRequests->create( $phone, array("friendlyName" => $friendlyName));
        $validationCode = $validationRequest->validationCode;
        echo "validationCode: $validationCode";
    }
    
    
    public function showList(){
        $outgoingCallerIds = $this->client->outgoingCallerIds->read(
            array("phoneNumber" => '+15168530252')
            );
        foreach ($outgoingCallerIds as $outgoingCallerId) {
            echo $outgoingCallerId->phoneNumber;
        }
    }
    

}

?>