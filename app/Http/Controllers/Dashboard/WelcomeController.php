<?php

namespace App\Http\Controllers\Dashboard;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Company;
use App\Cards;
use App\Order;
use App\Client;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class WelcomeController extends Controller
{
    public function index()
    {
        $orders = Order::count();
        $companies = Company::count();
        $cards = Cards::where(array('avaliable' => 0, 'purchase' => 0))->count();
        $clients = Client::count();
       

        $curl = curl_init();
        $refrenceid = "Merchant_" . rand();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://taxes.like4app.com/online/orders",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => array(
                'deviceId' => 'cd63173e952e3076462733a26c71bbd0b236291db71656ec65ee1552478402ef',
                'email' => 'info@bn-plus.ly',
                'password' => 'db7d8028631f3351731cf7ca0302651d',
                'securityCode' => 'cd63173e952e3076462733a26c71bbd077d972e07e1d416cb9ab7f87bfc0c014',
                'langId' => '1',

            ),

        ));

        $dubiorder = curl_exec($curl);
        curl_close($curl);
        $dubiordersjson = json_decode($dubiorder, true);


        dd($dubiordersjson );
if(isset($dubiordersjson['response'] )){


      
if($dubiordersjson['response'] ==1){
    $dubiorders= count( $dubiordersjson['data']) ;
  //  dd($dubiorders );
}else{
    $dubiorders= '';
}
}else{
    $dubiorders= ''; 
}


    
        return view('dashboard.welcome', compact('companies','orders','cards','clients','dubiorders'));

    }//end of index

}//end of controller
