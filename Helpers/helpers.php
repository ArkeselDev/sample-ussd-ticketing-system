<?php

namespace Helpers;

use Exception;


function send_sms($message, $phoneNumber)
{
    $senderID = 'MYSENDERID';
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => 'https://sms.arkesel.com/api/v2/sms/send',
        CURLOPT_HTTPHEADER => ['api-key: XYXYXYXYXYXYXYXYXYXYXY'],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => http_build_query([
            'sender' => $senderID,
            'message' => $message,
            'recipients' => is_array($phoneNumber) ? $phoneNumber : [$phoneNumber],
        ]),
    ]);    
    $response = curl_exec($curl);
    curl_close($curl);
    $response = json_decode($response);

    return $response;

}

function number_is_valid (string $phoneNumber) :bool
{
    $phoneNumber = str_replace([' ','-','(',')','+'],'',$phoneNumber);

    if (($length = strlen($phoneNumber)) < 9 || $length > 12)
        return false;

    if ($length = 12 && substr($phoneNumber,0,3) == '233')
        $phoneNumber = substr($phoneNumber,3,9);
        // $phoneNumber = str_replace('233','',$phoneNumber);

    if ($phoneNumber[0] == '0') 
        $phoneNumber = substr($phoneNumber,1,9);

    if (!in_array(substr($phoneNumber,0,2), ['54','24','53','59','55','50','20','26','27','28','29','...']))
        return false;

    return true;
}