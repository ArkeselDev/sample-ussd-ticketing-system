<?php

namespace App\Ussd;
require_once __DIR__ . "/../../Classes/DBCOnnection.php";
require_once __DIR__ . "/../../Classes/Session.php";

use Classes\DBConnection;
use Classes\Session;
use function Helpers\number_is_valid;

// use Resolvers\DBConnection;

// Get the incoming data
$arkeselInput = file_get_contents("php://input");
// decode the data 
$decodedInput = json_decode($arkeselInput);
// get session attr
$sessionId = $decodedInput->sessionID;
$userId = $decodedInput->userID;
$userInput = $decodedInput->userData;
$msisdn = $decodedInput->msisdn;
$isNewSession = $decodedInput->newSession;
$network = $decodedInput->network;
// Safely return false continueSession when forgotten
$continueSession = false;
$message = "";
$nextLevel = 0;
$nextStage = 0;

$connectdb = new DBConnection("localhost","root","","ticketing_ussd_app");
if (!$connectdb->connect()) 
    return http_response_code(502);

$session = new Session($sessionId,$msisdn,$connectdb);
if (!$session->sessionExists()) {
    $sessionCreated = $session->createSession(0,0);

    if (!$sessionCreated)
        return http_response_code(502);
}

    
// USSD Logic
if ($isNewSession || ($session->sessionData["level"] == 0 && $session->sessionData["stage"] == 0))
{

    $message = "Welcome to Arkebits Bus Tickets Platform\n";
    $message = "Select which city you want to buy a bus ticket to\n\n";
    $message .= "1. Kumasi\n";
    $message .= "2. Accra\n";
    $message .= "3. Tarkoradi\n";
    $message .= "4. Cape Coast\n";
    $message .= "5. Koforidua\n";
    $message .= "6. Winneba\n";

    $nextLevel = 1;
    $nextStage = 1;
    $continueSession = true;

} else {
    
    $level = $session->sessionData["level"];
    $stage = $session->sessionData["stage"];
    
    // Level 1 Menu --> Stages
    if ($level == 1) {
        if ($stage == 1) {

            if ($userInput < 1 || $userInput > 6) {
                $message = "Please enter an option number between 1 and 6";
                $continueSession = false;
                
            } else {
                $city = $userInput; 
                switch ($userInput) {
                    case "1":
                        $city = "Kumasi";
                        break;
                    case "2":
                        $city = "Accra";
                        break;
                    case "3":
                        $city = "Tarkoradi";
                        break;  
                    case "4":
                        $city = "Cape Coast";
                        break;  
                    case "5":
                        $city = "Koforidua";
                        break;
                    case "6":
                        $city = "Winneba";
                        break;  
                    default:
                        break;
                }
                $message = "How many bus tickets to ".$city." do you want to buy ?\n";
                $message .= "Cost: Ghs 120.00 per ticket\n";
                $userInput = ["city" => $city];
                $continueSession = true;

                $nextLevel = 1;
                $nextStage = 2;
            }
        } else if ($stage == 2) {

            $userInput = (int) $userInput; 

            if (!is_int($userInput)) {
                $message = "Invalid amount value, please enter an amount between 1 and 25\n";
                $continueSession = false;
                
            } else if ($userInput < 1 || $userInput > 26) {
                $message = "Sorry, the amount of tickets you can purchase at this time cannot be more than 26";
                $continueSession = false;
                
            } else {
                $message = "Which phone number do you want to use for payment ?\n";
                $continueSession = true;

                $nextLevel = 1;
                $nextStage = 3;
                $userInput = ["amount" => $userInput];
            }

        } else if ($stage == 3) {

            if (! number_is_valid($userInput)) {
                $message = "Phone number is invalid, unable to process payment. Please try again.";
                $continueSession = false;

            } else {
                
                $paymentNumber = $userInput;
                $calculatedCharge = ($amount = json_decode($session->sessionData["data"],true)["amount"]) * 120;
                $city = json_decode($session->sessionData["data"],true)["city"];
                $message = "You are about to make a payment of GHs".$calculatedCharge." for ".$amount." tickets to ".$city."\n";
                $message .= "\nPayment number: ". $paymentNumber.". Continue ?\n";
                $message .= "1. Yes, proceed\n";
                $message .= "2. No, cancel";

                $continueSession = true;
                $nextLevel = 1;
                $nextStage = 4;
                $userInput = ["payment_number" => $paymentNumber];
            }

        } else if ($stage == 4) {

            if ($userInput != 2 && $userInput != 1) {
                $message = "Your input could not be validated";
                $continueSession = false;
                $nextLevel = 0;
                $nextStage = 0;

            } else if ($userInput == 2) {
                $message = "The purchase attempt has been canceled.";
                $continueSession = false;
                $nextLevel = 0;
                $nextStage = 0;

            } else if ($userInput == 1) {
                $message = "A payment prompt is being sent to you now.\n";
                $message .= "You will receive your tickets immediately your payment transaction is confirmed.\nThank you!";
                $continueSession = false;
                $nextLevel = 0;
                $nextStage = 0;

            }

        } else {
            
            $message = "Invalid choice. Please try again.";
            $continueSession = false;
            $nextLevel = 0;
            $nextStage = 0;
        }

    } else {

        $message = "Unknown option";
        $continueSession = false;
        $nextLevel = 0;
        $nextStage = 0;

    }

}

if (!is_array($userInput))
    $userInput = ["input" => $userInput];


// update the session data
$session->updateSession($nextLevel,$nextStage,$userInput);

// Close DB connection
$connectdb->close();

$output = [
    "sessionID" => $sessionId,
    "userID" => $userId,
    "msisdn" => $msisdn,
    "message" => $message,
    "continueSession" => $continueSession
  ];

// Return HTTP200 for success
http_response_code(200);
// sending back reponse as json
header("Content-Type: application/json");
// send encoded output of the reqeust response 
echo json_encode($output); 


