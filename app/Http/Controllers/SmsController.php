<?php

namespace App\Http\Controllers;

use Infobip\Api\SmsApi;
use Infobip\ApiException;
use Infobip\Configuration;
use Illuminate\Http\Request;
use Infobip\Model\SmsDestination;
use Infobip\Model\SmsTextualMessage;
use AfricasTalking\SDK\AfricasTalking;
use Symfony\Component\Process\Process;
use Infobip\Model\SmsAdvancedTextualRequest;
use Symfony\Component\Process\Exception\ProcessFailedException;

class SmsController extends Controller
{

 

public function sendSms(){
    $username = 'phabianmalonja';

    $apiKey   = 'atsk_008f794596ae78dc0dbdb2e7fac8549283e480ecf30062c3d678157c9042055ee5e77f4f';

    $AT = new AfricasTalking($username,$apiKey);
    
    $sms      = $AT->sms();

// Use the service
$result   = $sms->send([
    'to'      => '+255764550006',
    'message' => 'Hello from ths shulemis app '
]);

return "message sent succes";

}


    public function getRegions()
    {
        $process = new Process(['node', base_path('geo-api.js'), 'regions']);
        $process->run();

        // Check for errors
        if (!$process->isSuccessful()) {
            return response()->json(['error' => 'Failed to fetch regions'], 500);
        }

        return response()->json(json_decode($process->getOutput()), 200);
    }

    public function getDistricts(Request $request)
    {
        $region = $request->input('region');
        $process = new Process(['node', base_path('getTanzaniaData.js'), 'districts', $region]);
        $process->run();

        if (!$process->isSuccessful()) {
            return response()->json(['error' => 'Failed to fetch districts'], 500);
        }

        return response()->json(json_decode($process->getOutput()), 200);
    }

}
