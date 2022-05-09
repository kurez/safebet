<?php


namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use View;
use File;


class MpesaController extends Controller
{

    
    # Lipa na M-PESA password 
    public function lipaNaMpesaPassword()
    {
        $lipa_time = Carbon::rawParse('now')->format('YmdHms');
        $passkey = "bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919";
        $BusinessShortCode = 174379;
        $timestamp =$lipa_time;
        $lipa_na_mpesa_password = base64_encode($BusinessShortCode.$passkey.$timestamp);
        return $lipa_na_mpesa_password;
    }
    
    # Lipa na M-PESA STK Push method
    public function customerMpesaSTKPush()
    {
        $url = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Authorization:Bearer '.$this->generateAccessToken()));
        $curl_post_data = [
            //Fill in the request parameters with valid values
            'BusinessShortCode' => 174379,
            'Password' => $this->lipaNaMpesaPassword(),
            'Timestamp' => Carbon::rawParse('now')->format('YmdHms'),
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => 1,
            'PartyA' => 254708746046, // replace this with your phone number
            'PartyB' => 174379,
            'PhoneNumber' => 254708746046, // replace this with your phone number
            'CallBackURL' => 'https://d16a-154-159-248-45.ngrok.io/api/v1/safebet/subscription/callback',
            'AccountReference' => "Safebet",
            'TransactionDesc' => "Testing stk push for safebet"
        ];
        $data_string = json_encode($curl_post_data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        // $curl_response = curl_exec($curl);
        $curl_response = json_decode(curl_exec($curl));

        $array = get_object_vars($curl_response);
        // echo $array["ResponseCode"];
        
        return View::make('stkResponse')->with('data', $array);
      

    }
    public function subscriptionCallback (Request $request)
    {
        $content=json_decode($request->getContent());
        $content = get_object_vars($content);
        var_dump($content);
        $content = $content["Body"]->stkCallback;
        $file = time() .rand(). '_file.json';
        $destinationPath=public_path()."/file/";
        if (!is_dir($destinationPath)) {  mkdir($destinationPath,0777,true);  }
        File::put($destinationPath.$file,json_encode($content));
    } 

    # access token function
    public function generateAccessToken()
    {
        $consumer_key="vvj1GA527zZjbESuJCzCo8dVAMZk226W";
        $consumer_secret="FZJPCNsDLtnIX8AP";
        $credentials = base64_encode($consumer_key.":".$consumer_secret);
        $url = "https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Authorization: Basic ".$credentials));
        curl_setopt($curl, CURLOPT_HEADER,false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $curl_response = curl_exec($curl);
        $access_token=json_decode($curl_response);
        return $access_token->access_token;
    }


}

