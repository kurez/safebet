<?php

namespace App\Http\Controllers;

use App\MpesaStkPush;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Session;

class TransactionController extends Controller
{


    //Initiate STK Push
    public function stkPushRequest(Request $request){

        $accountReference='Transaction#'.Str::random(10);

        // $amount= $request->amount;
        $amount = 1;
        // $phone=$this->formatPhone($request->phone_number);
        $phone=254708746046;

        $mpesa=new MpesaStkpush();
        $stk=$mpesa->lipaNaMpesa($amount,$phone,$accountReference);
        $invalid=json_decode($stk);

        if(@$invalid->errorCode){
            Session::flash('mpesa-error', 'Invalid phone number!');
            Session::flash('alert-class', 'alert-danger');

            return back();
        }

        return redirect('/confirm/'.encrypt($accountReference));
    }

    public function checkTransactionStatus($transactionCode){

        $mpesa=new MpesaStkpush();
        $status=$mpesa->status($transactionCode);

        $tStatus = $status->{'ResponseCode'};

        return $tStatus;
    }

    public function formatPhone($phone)
    {
        $phone = 'hfhsgdgs' . $phone;
        $phone = str_replace('hfhsgdgs0', '', $phone);
        $phone = str_replace('hfhsgdgs', '', $phone);
        $phone = str_replace('+', '', $phone);
        if (strlen($phone) == 9) {
            $phone = '254' . $phone;
        }
        return $phone;
    }

}