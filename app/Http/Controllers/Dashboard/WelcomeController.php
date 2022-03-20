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
        $companies = Company::where('enable',0)->count();
        $cards = Cards::where(array('avaliable' => 0, 'purchase' => 0,'enable'=>0))->count();
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


       // dd($dubiorder );
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


$uri = 'https://identity.anis.ly/connect/token';
$params = array(
    'grant_type' => 'user_credentials',
    'client_id' => 'bn-plus',
    'client_secret' => '3U8F3U9C9IM39VJ39FUCLWLC872MMXOW8K2STWI28ZJD3ERF',
    'password' => 'P@ssw0rd1988',
    'email' => 'info@bn-plus.ly',
);
$response = Http::asForm()->withHeaders([])->post($uri, $params);   
$token=$response->json()['access_token'];
$token_type=$response->json()['token_type'];
$alltoken=$response->json()['token_type'] .' '.$response->json()['access_token'];



$swaggercompanies = Http::withHeaders([
    'Accept' => 'application/json',
    'Authorization' => $alltoken,
])->get('https://gateway.anis.ly/api/consumers/v1/transactions?pinNumber=1988');


$alldata=$swaggercompanies->json()['data'];







    
        return view('dashboard.welcome', compact('companies','orders','cards','clients','dubiorders','alldata'));

    }//end of index

}//end of controller
