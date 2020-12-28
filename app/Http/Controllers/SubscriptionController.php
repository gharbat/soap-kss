<?php

namespace App\Http\Controllers;

use App\Subscription;
use Auth;
use SimpleXMLElement;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Carbon\Carbon;

/**
 * Class SubscriptionController
 * @package App\Http\Controllers
 */
class SubscriptionController extends Controller
{

    /**
     * @param $action_id
     */
    public function action($action_id)
    {
        switch ($action_id) {
            case 1:
                $this->activate();
                break;
            case 2:
                $this->HardCancel();
                break;
            case 3:
                $this->softCancel();
                break;
            case 4:
                $this->GetInfo();
                break;
            case 5:
                $this->Pause();
                break;
            default:
                abort(404);
                exit();
        }

    }

    /**
     * This method will activate account via KSS
     */
    public function activate()
    {
//        date_default_timezone_set('America/Los_Angeles');


        $uuid = $this->sub_id();
        $timestamp = Carbon::now()->toIso8601String();
        $end= Carbon::now()->addYear()->toIso8601String();

        $subscripID = $this->sub_id();
        $username = env("KSS_NAME", "Asiacell_SMT_1309");
        $password = env("KSS_PASSWORD", "smt@123SMT!@#$%^");

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
                CURLOPT_POSTFIELDS => "<?xml version='1.0' encoding='UTF-8'?>\n<soapenv:Envelope \n    xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\">\n    <soapenv:Header>\n        <AccessInfo \n            xmlns=\"http://schemas.kaspersky.com/services/subscription/2.0/\">\n            <UserName>$username</UserName>\n            <Password>$password</Password>\n        </AccessInfo>\n    </soapenv:Header>\n    <soapenv:Body>\n        <SubscriptionRequestContainer \n            xmlns=\"http://schemas.kaspersky.com/services/subscription/2.0/\">\n            <Timestamp>$timestamp</Timestamp>\n            <TransactionId>$uuid</TransactionId>\n            <SubscriptionRequest>\n                <Activate UnitId=\"1\" SubscriberId=\"$subscripID\"  ActivationType=\"Standard\" StartTime=\"2020-11-16T08:06:00Z\" EndTime=\"2020-11-17T15:06:00Z\" LicenseCount=\"2\" ProductId=\"KTSMD\"/>\n            </SubscriptionRequest>\n        </SubscriptionRequestContainer>\n    </soapenv:Body>\n</soapenv:Envelope>",
                CURLOPT_HTTPHEADER => array(
                    "Content-Type: application/xml"
                ),
            ));


            $response = curl_exec($curl);

            curl_close($curl);

            /*
             * @TODO parse $response in xml
             */

            $subscription = new Subscription();
            $subscription->subscribe_id = $this->sub_id();
            $subscription->action = 1;
            $subscription->result = $response;
            $subscription->save();


            $xml = simplexml_load_string($response, "SimpleXMLElement", LIBXML_NOCDATA);
            $json = json_encode($xml);
            $array = json_decode($json,TRUE);
            echo "<pre>";
            var_dump($response);
            var_dump($xml);
            echo "</pre>";

        } catch (HttpException $e) {
            var_dump("Error In PHP Request");
        }
    }

    /**
     * @return mixed
     */
    public function sub_id()
    {
        return "Auth::user()->subscriber_id";
    }

    /**
     * @return bool|string
     */
    public function HardCancel()
    {
        $uuid = $this->sub_id();

        $timestamp = now();
        $username = env("KSS_NAME", "Asiacell_SMT_1309");
        $password = env("KSS_PASSWORD", "smt@123SMT!@#$%^");

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
                CURLOPT_POSTFIELDS => "<?xml version='1.0' encoding='UTF-8'?>\n<soapenv:Envelope \n    xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\">\n    <soapenv:Header>\n        <AccessInfo \n            xmlns=\"http://schemas.kaspersky.com/services/subscription/2.0/\">\n            <UserName>$username</UserName>\n            <Password>$password</Password>\n        </AccessInfo>\n    </soapenv:Header>\n    <soapenv:Body>\n        <SubscriptionRequestContainer \n            xmlns=\"http://schemas.kaspersky.com/services/subscription/2.0/\">\n            <Timestamp>$timestamp</Timestamp>\n            <TransactionId>$uuid</TransactionId>\n            <SubscriptionRequest>\n  <HardCancel UnitId=\"9\" SubscriberId=\"D4567E02-8A91-43B2-AD48-5D04DAFD38C3\" EndTime=\"2020-11-15T18:00:00Z\"/>    </SubscriptionRequest>\n        </SubscriptionRequestContainer>\n    </soapenv:Body>\n</soapenv:Envelope>",
                CURLOPT_HTTPHEADER => array(
                    "Content-Type: application/xml"
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            $subscription = new Subscription();
            $subscription->subscribe_id = $this->sub_id();
            $subscription->save();
            return $response;
        } catch (HttpException $e) {
            var_dump("Error In PHP Request");
        }
    }

    /**
     * @return bool|string
     */
    public function softCancel()
    {

        $uuid = $this->sub_id();

        $timestamp = now();
        $username = env("KSS_NAME", "Asiacell_SMT_1309");
        $password = env("KSS_PASSWORD", "smt@123SMT!@#$%^");

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
                CURLOPT_POSTFIELDS => "<?xml version='1.0' encoding='UTF-8'?>\n<soapenv:Envelope \n    xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\">\n    <soapenv:Header>\n        <AccessInfo \n            xmlns=\"http://schemas.kaspersky.com/services/subscription/2.0/\">\n            <UserName>$username</UserName>\n            <Password>$password</Password>\n        </AccessInfo>\n    </soapenv:Header>\n    <soapenv:Body>\n        <SubscriptionRequestContainer \n            xmlns=\"http://schemas.kaspersky.com/services/subscription/2.0/\">\n            <Timestamp>$timestamp</Timestamp>\n            <TransactionId>$uuid</TransactionId>\n            <SubscriptionRequest>\n                <SoftCancel UnitId=\"6\" SubscriberId=\"D4567E02-8A91-43B2-AD48-5D04DAFD38C3\" EndTime=\"2020-11-15T18:00:00Z\"/>\n            </SubscriptionRequest>\n        </SubscriptionRequestContainer>\n    </soapenv:Body>\n</soapenv:Envelope>",
                CURLOPT_HTTPHEADER => array(
                    "Content-Type: application/xml"
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            $subscription = new Subscription();
            $subscription->subscribe_id = $this->sub_id();
            $subscription->save();
            return $response;
        } catch (HttpException $e) {
            var_dump("Error In PHP Request");
        }
    }

    /**
     * @return bool|string
     */
    public function GetInfo()
    {
        $uuid = $this->sub_id();

        $timestamp = now();
        $username = env("KSS_NAME", "Asiacell_SMT_1309");
        $password = env("KSS_PASSWORD", "smt@123SMT!@#$%^");

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
                CURLOPT_POSTFIELDS => "<?xml version='1.0' encoding='UTF-8'?>\n<soapenv:Envelope \n    xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\">\n    <soapenv:Header>\n        <AccessInfo \n            xmlns=\"http://schemas.kaspersky.com/services/subscription/2.0/\">\n            <UserName>$username</UserName>\n            <Password>$password</Password>\n        </AccessInfo>\n    </soapenv:Header>\n    <soapenv:Body>\n        <SubscriptionRequestContainer \n            xmlns=\"http://schemas.kaspersky.com/services/subscription/2.0/\">\n            <Timestamp>$timestamp</Timestamp>\n            <TransactionId>$uuid</TransactionId>\n            <SubscriptionRequest>\n    <GetInfo UnitId=\"13\" SubscriberId=\"4810fc6f-f25e-430d-bee7-319fba4644bd\" InfoSection=\"Subscription\" />    </SubscriptionRequest>\n        </SubscriptionRequestContainer>\n    </soapenv:Body>\n</soapenv:Envelope>",
                CURLOPT_HTTPHEADER => array(
                    "Content-Type: application/xml"
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            $subscription = new Subscription();
            $subscription->subscribe_id = $this->sub_id();
            $subscription->save();
            return $response;
        } catch (HttpException $e) {
            var_dump("Error In PHP Request");
        }
    }

    /**
     * @return bool|string
     */
    public function Pause()
    {
        $uuid = $this->sub_id();

        $timestamp = now();
        $username = env("KSS_NAME", "Asiacell_SMT_1309");
        $password = env("KSS_PASSWORD", "smt@123SMT!@#$%^");

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
                CURLOPT_POSTFIELDS => "<?xml version='1.0' encoding='UTF-8'?>\n<soapenv:Envelope \n    xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\">\n    <soapenv:Header>\n        <AccessInfo \n            xmlns=\"http://schemas.kaspersky.com/services/subscription/2.0/\">\n            <UserName>$username</UserName>\n            <Password>$password</Password>\n        </AccessInfo>\n    </soapenv:Header>\n    <soapenv:Body>\n        <SubscriptionRequestContainer \n            xmlns=\"http://schemas.kaspersky.com/services/subscription/2.0/\">\n            <Timestamp>$timestamp</Timestamp>\n            <TransactionId>$uuid</TransactionId>\n            <SubscriptionRequest>\n  <Pause UnitId=\"10\" SubscriberId=\"D4567E02-8A91-43B2-AD48-5D04DAFD38C3\" PauseTime=\"2020-11-15T18:00:00Z\"/>    </SubscriptionRequest>\n        </SubscriptionRequestContainer>\n    </soapenv:Body>\n</soapenv:Envelope>",
                CURLOPT_HTTPHEADER => array(
                    "Content-Type: application/xml"
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            $subscription = new Subscription();
            $subscription->subscribe_id = $this->sub_id();
            $subscription->save();
            return $response;
        } catch (HttpException $e) {
            var_dump("Error In PHP Request");
        }
    }

    /**
     * @return bool|string
     */
    public function Renew()
    {
        $uuid = $this->sub_id();

        $timestamp = now();
        $username = env("KSS_NAME", "Asiacell_SMT_1309");
        $password = env("KSS_PASSWORD", "smt@123SMT!@#$%^");

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
                CURLOPT_POSTFIELDS => "<?xml version='1.0' encoding='UTF-8'?>\n<soapenv:Envelope \n    xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\">\n    <soapenv:Header>\n        <AccessInfo \n            xmlns=\"http://schemas.kaspersky.com/services/subscription/2.0/\">\n            <UserName>$username</UserName>\n            <Password>$password</Password>\n        </AccessInfo>\n    </soapenv:Header>\n    <soapenv:Body>\n        <SubscriptionRequestContainer \n            xmlns=\"http://schemas.kaspersky.com/services/subscription/2.0/\">\n            <Timestamp>$timestamp</Timestamp>\n            <TransactionId>$uuid</TransactionId>\n            <SubscriptionRequest>\n       <Renew UnitId=\"3\" SubscriberId=\"D4567E02-8A91-43B2-AD48-5D04DAFD38C3\" EndTime=\"indefinite\"/>    </SubscriptionRequest>\n        </SubscriptionRequestContainer>\n    </soapenv:Body>\n</soapenv:Envelope>",
                CURLOPT_HTTPHEADER => array(
                    "Content-Type: application/xml"
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            $subscription = new Subscription();
            $subscription->subscribe_id = $this->sub_id();
            $subscription->save();
            return $response;
        } catch (HttpException $e) {
            var_dump("Error In PHP Request");
        }
    }

    /**
     * @return bool|string
     */
    public function Resume()
    {

        $uuid = $this->sub_id();

        $timestamp = now();
        $username = env("KSS_NAME", "Asiacell_SMT_1309");
        $password = env("KSS_PASSWORD", "smt@123SMT!@#$%^");

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
                CURLOPT_POSTFIELDS => "<?xml version='1.0' encoding='UTF-8'?>\n<soapenv:Envelope \n    xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\">\n    <soapenv:Header>\n        <AccessInfo \n            xmlns=\"http://schemas.kaspersky.com/services/subscription/2.0/\">\n            <UserName>$username</UserName>\n            <Password>$password</Password>\n        </AccessInfo>\n    </soapenv:Header>\n    <soapenv:Body>\n        <SubscriptionRequestContainer \n            xmlns=\"http://schemas.kaspersky.com/services/subscription/2.0/\">\n            <Timestamp>$timestamp</Timestamp>\n            <TransactionId>$uuid</TransactionId>\n            <SubscriptionRequest>\n                       <Resume UnitId=\"8\" SubscriberId=\"D4567E02-8A91-43B2-AD48-5D04DAFD38C3\" />    </SubscriptionRequest>\n        </SubscriptionRequestContainer>\n    </soapenv:Body>\n</soapenv:Envelope>",
                CURLOPT_HTTPHEADER => array(
                    "Content-Type: application/xml"
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            $subscription = new Subscription();
            $subscription->subscribe_id = $this->sub_id();
            $subscription->save();
            return $response;
        } catch (HttpException $e) {
            var_dump("Error In PHP Request");
        }
    }

    /**
     * @return bool|string
     */
    public function GetDownloadLinks()
    {
        $uuid = $this->sub_id();

        $timestamp = now();
        $username = env("KSS_NAME", "Asiacell_SMT_1309");
        $password = env("KSS_PASSWORD", "smt@123SMT!@#$%^");

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
                CURLOPT_POSTFIELDS => "<?xml version='1.0' encoding='UTF-8'?>\n<soapenv:Envelope \n    xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\">\n    <soapenv:Header>\n        <AccessInfo \n            xmlns=\"http://schemas.kaspersky.com/services/subscription/2.0/\">\n            <UserName>$username</UserName>\n            <Password>$password</Password>\n        </AccessInfo>\n    </soapenv:Header>\n    <soapenv:Body>\n        <SubscriptionRequestContainer \n            xmlns=\"http://schemas.kaspersky.com/services/subscription/2.0/\">\n            <Timestamp>$timestamp</Timestamp>\n            <TransactionId>$uuid</TransactionId>\n            <SubscriptionRequest>\n                    <GetDownloadLinks UnitId=\"11\" SubscriberId=\"D4567E02-8A91-43B2-AD48-5D04DAFD38C3\" Language=\"ru\" Alphabet=\"Cyrl\" Region=\"RU\" RebrandingCode=\"ruRU\" Platform=\"Windows\"/>    </SubscriptionRequest>\n        </SubscriptionRequestContainer>\n    </soapenv:Body>\n</soapenv:Envelope>",
                CURLOPT_HTTPHEADER => array(
                    "Content-Type: application/xml"
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            $subscription = new Subscription();
            $subscription->subscribe_id = $this->sub_id();
            $subscription->save();
            return $response;
        } catch (HttpException $e) {
            var_dump("Error In PHP Request");
        }
    }

}


