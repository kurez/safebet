<?php

namespace App;
use Carbon\Carbon;
use function Psy\sh;


use Illuminate\Database\Eloquent\Model;

class MpesaStkPush extends Model
{
    protected $consumer_key;
    protected $consumer_secret;
    protected $passkey;
    protected $amount;
    protected $accountReference;
    protected $phone;
    protected $env;
    protected $short_code;
    protected $parent_short_code;
    protected $initiatorName;
    protected $initiatorPassword ;

    public function __construct(){

        $this->short_code = '174379';
        $this->parent_short_code='174379';
        $this->consumer_key="vvj1GA527zZjbESuJCzCo8dVAMZk226W"; //Your Consumer key
        $this->consumer_secret="FZJPCNsDLtnIX8AP"; //Your Secret key
        $this->passkey = "bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919"; //Your Passkey
        $this->CallBackURL = "https://d16a-154-159-248-45.ngrok.io/api/v1/safebet/subscription/callback"; //Your callback URL
        $this->env = "sandbox"; //Your Environment sandbox or Live
        $this->initiatorName = "Safebet"; //Username of your choice
        $this->initiatorPassword = "Safaricom978!"; //Password of your choice

    }

    /** Lipa na M-PESA password **/
    // public function getPassword()
    // {
    //     $timestamp = Carbon::now()->format('YmdHms');
    //     $password  = base64_encode($this->short_code. "" . $this->passkey ."". $timestamp);

    //     return $password;
    // }

    public function lipaNaMpesa($amount,$phone,$accountReference){
        $this->phone = $phone;
        $this->amount= $amount;
        $this->accountReference= $accountReference;

        $timestamp = Carbon::now()->format('YmdHms');
        $password  = base64_encode($this->short_code. "" . $this->passkey ."". $timestamp);


        $headers = ['Content-Type:application/json; charset=utf8'];

        $access_token_url = ($this->env  == "live") ? "https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials" : "https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials"; 
        $initiate_url = ($this->env == "live") ? "https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest" : "https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest"; 


        $curl = curl_init($access_token_url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_HEADER, FALSE);
        curl_setopt($curl, CURLOPT_USERPWD, $this->consumer_key.':'.$this->consumer_secret);
        $result = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $result = json_decode($result);
        $access_token = $result->access_token;
        curl_close($curl);


        # header for stk push
        $stkheader = ['Content-Type:application/json','Authorization:Bearer '.$access_token];
        # initiating the transaction
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $initiate_url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $stkheader); //setting custom header

        $curl_post_data = array(
            //Fill in the request parameters with valid values
            // 'BusinessShortCode' => 174379,
            // 'Password' => $this->lipaNaMpesaPassword(),
            // 'Timestamp' => Carbon::rawParse('now')->format('YmdHms'),
            // 'TransactionType' => 'CustomerPayBillOnline',
            // 'Amount' => 1,
            // 'PartyA' => 254708746046, // replace this with your phone number
            // 'PartyB' => 174379,
            // 'PhoneNumber' => 254708746046, // replace this with your phone number
            // 'CallBackURL' => 'https://d16a-154-159-248-45.ngrok.io/api/v1/safebet/subscription/callback',
            // 'AccountReference' => "Safebet",
            // 'TransactionDesc' => "Testing stk push for safebet"
            'BusinessShortCode' => $this->short_code,
            'Password' => $password,
            'Timestamp' => Carbon::rawParse('now')->format('YmdHms'),
            'TransactionType' => 'CustomerBuyGoodsOnline',
            'Amount' => $this->amount,
            'PartyA' => $phone,
            'PartyB' => $this->parent_short_code,
            'PhoneNumber' => $phone,
            'CallBackURL' => $this->CallBackURL,
            'AccountReference' => $this->accountReference,
            'TransactionDesc' => $phone." has paid ".$this->amount." to ".$this->short_code
        );

        $data_string = json_encode($curl_post_data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
        $response = curl_exec($curl);

        return $response;
    }

    public function status($transactionCode){
        $type = 4;
        $command = "TransactionStatusQuery";
        $remarks = "Transaction Status Query"; 
        $occasion = "Transaction Status Query";
        $results_url = "https://d16a-154-159-248-45.ngrok.io/TransactionStatus/result/"; //Endpoint to receive results Body
        $timeout_url = "https://d16a-154-159-248-45.ngrok.io/TransactionStatus/queue/"; //Endpoint to to go to on timeout

        $access_token = ($this->env == "live") ? "https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials" : "https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials"; 
        $credentials = base64_encode($this->consumer_key . ':' . $this->consumer_secret); 
        
        $ch = curl_init($access_token);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Basic " . $credentials]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        $response = curl_exec($ch);
        curl_close($ch);
        
        $result = json_decode($response); 

        //echo $result->{'access_token'};
        
        $token = isset($result->{'access_token'}) ? $result->{'access_token'} : "N/A";

        $publicKey = file_get_contents(__DIR__ . "/mpesa_public_cert.cer"); 
        $isvalid = openssl_public_encrypt($this->initiatorPassword, $encrypted, $publicKey, OPENSSL_PKCS1_PADDING); 
        $password = base64_encode($encrypted);

        //echo $token;

        $curl_post_data = array( 
            "Initiator" => $this->initiatorName, 
            "SecurityCredential" => $password, 
            "CommandID" => $command, 
            "TransactionID" => $transactionCode, 
            "PartyA" => $this->short_code, 
            "IdentifierType" => $type, 
            "ResultURL" => $results_url, 
            "QueueTimeOutURL" => $timeout_url, 
            "Remarks" => $remarks, 
            "Occasion" => $occasion,
        ); 

        $data_string = json_encode($curl_post_data);

        //echo $data_string;

        $endpoint = ($env == "live") ? "https://api.safaricom.co.ke/mpesa/transactionstatus/v1/query" : "https://sandbox.safaricom.co.ke/mpesa/transactionstatus/v1/query"; 

        $ch2 = curl_init($endpoint);
        curl_setopt($ch2, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer '.$token,
            'Content-Type: application/json'
        ]);
        curl_setopt($ch2, CURLOPT_POST, 1);
        curl_setopt($ch2, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch2, CURLOPT_RETURNTRANSFER, 1);
        $response     = curl_exec($ch2);
        curl_close($ch2);

        //echo "Authorization: ". $response;

        $result = json_decode($response); 
        
        return $result;
}
}