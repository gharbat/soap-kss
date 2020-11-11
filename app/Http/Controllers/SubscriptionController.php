<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class SubscriptionController
 * @package App\Http\Controllers
 */
class SubscriptionController extends Controller
{
    /**
     * This method will activate account via KSS
     */
    public function activate()
    {
        $uuid = $this->gen_uuid();
        $timestamp = now();
        $username = env("KSS_NAME", "xxx");
        $password = env("KSS_PASSWORD", "xxx");

        try {

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://test.kss.kaspersky-labs.com:443/soap/upi",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS =>"<?xml version='1.0' encoding='UTF-8'?>\n<soapenv:Envelope \n    xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\">\n    <soapenv:Header>\n        <AccessInfo \n            xmlns=\"http://schemas.kaspersky.com/services/subscription/2.0/\">\n            <UserName>$username</UserName>\n            <Password>$password</Password>\n        </AccessInfo>\n    </soapenv:Header>\n    <soapenv:Body>\n        <SubscriptionRequestContainer \n            xmlns=\"http://schemas.kaspersky.com/services/subscription/2.0/\">\n            <Timestamp>$timestamp</Timestamp>\n            <TransactionId>$uuid</TransactionId>\n            <SubscriptionRequest>\n                <Activate UnitId=\"1\" SubscriberId=\"$uuid\" ActivationType=\"Standard\" StartTime=\"$timestamp\" EndTime=\"$timestamp\" LicenseCount=\"1\" ProductId=\"KIS\"/>\n            </SubscriptionRequest>\n        </SubscriptionRequestContainer>\n    </soapenv:Body>\n</soapenv:Envelope>",
                CURLOPT_HTTPHEADER => array(
                    "Content-Type: application/xml"
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            $subscription = new Subscription();
            $subscription->subscribe_id = $uuid;
            $subscription->save();

            return $response;
        }catch (HttpException $e){
            var_dump("Error In PHP Request");
        }
    }


    public function softCancel()
    {


        $uuid = $this->gen_uuid();
        $timestamp = now();
        $username = env("KSS_NAME", "xxx");
        $password = env("KSS_PASSWORD", "xxx");

        try {

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://test.kss.kaspersky-labs.com:443/soap/upi",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS =>"<?xml version='1.0' encoding='UTF-8'?>\n<soapenv:Envelope \n    xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\">\n    <soapenv:Header>\n        <AccessInfo \n            xmlns=\"http://schemas.kaspersky.com/services/subscription/2.0/\">\n            <UserName>$username</UserName>\n            <Password>$password</Password>\n        </AccessInfo>\n    </soapenv:Header>\n    <soapenv:Body>\n        <SubscriptionRequestContainer \n            xmlns=\"http://schemas.kaspersky.com/services/subscription/2.0/\">\n            <Timestamp>$timestamp</Timestamp>\n            <TransactionId>$uuid</TransactionId>\n            <SubscriptionRequest>\n                <SoftCancel UnitId=\"6\" SubscriberId=\"$uuid\" EndTime=\"$timestamp\"/>\n            </SubscriptionRequest>\n        </SubscriptionRequestContainer>\n    </soapenv:Body>\n</soapenv:Envelope>",
                CURLOPT_HTTPHEADER => array(
                    "Content-Type: application/xml"
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            $subscription = new Subscription();
            $subscription->subscribe_id = $uuid;
            $subscription->save();

            return $response;
        }catch (HttpException $e){
            var_dump("Error In PHP Request");
        }
    }

    public function gen_uuid() {
        return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            // 32 bits for "time_low"
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

            // 16 bits for "time_mid"
            mt_rand( 0, 0xffff ),

            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand( 0, 0x0fff ) | 0x4000,

            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand( 0, 0x3fff ) | 0x8000,

            // 48 bits for "node"
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
        );
    }
}
