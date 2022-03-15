<?php

namespace App\Http\Controllers\Dashboard;

use App\Company;
use App\Cards;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use PDF2;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class DubaiOrdersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       
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


        if(isset($dubiordersjson['response'] )){
      
if($dubiordersjson['response'] ==1){
    $dubiorders=  $dubiordersjson['data'] ;
  //  dd($dubiorders );
}else{
    $dubiorders= '';
}

        }else{

            $dubiorders= '';
        }
     
      
        $cardapi=Company::where(array('kind'=>'national','api'=>1))->orderBy('id','desc')->first();
        $cardnot=Company::where(array('kind'=>'national','api'=>0))->orderBy('id','desc')->first();
        $cardswagger=Company::where(array('api2'=>1))->orderBy('id','desc')->first();
       
        return view('dashboard.dubiorders.index', compact('dubiorders','cardapi','cardnot','cardswagger'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function dubiorders($order)
    {
     $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://taxes.like4app.com/online/orders/details/",
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
                    'orderId'=>$order
            ),

        ));

        $balancenational = curl_exec($curl);
        $json = json_decode($balancenational, true);
      // return $json;
$serials=$json['serials'];
    
      foreach ($serials as $row){

$code=$this->decryptSerial( $row['serialCode']); 
$product=$row['productName'];
$validTo=$row['validTo'];
        return view('dashboard.dubiorders.dubaiorderdetails', compact('code','product','validTo'));

    }

    

    }//end of products



    function decryptSerial($encrypted_txt){    
        $secret_key = 't-3zafRa';    
        $secret_iv = 'St@cE4eZ';
        $encrypt_method = 'AES-256-CBC';                
        $key = hash('sha256', $secret_key);        
      
        //iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning          
        $iv = substr(hash('sha256', $secret_iv), 0, 16);        
      
        return openssl_decrypt(base64_decode($encrypted_txt), $encrypt_method, $key, 0, $iv);        
      }
      
    //  echo decryptSerial('bnY0UEc2NFcySHgwRTIyNFU1NU5pUT09'); 

public function enableapi(Request $request){


$card=Cards::where(array('nationalcompany'=>'national','api'=>1))->orderBy('id','desc')->first();

$company=Company::where(array('kind'=>'national','api'=>1))->orderBy('id','desc')->first();


if($company->enable ==0){
    $updatenationalcompany['enable']=1;
}else{
    $updatenationalcompany['enable']=0;
}

if($card->enable ==0){
    $updatenational['enable']=1;
}else{
    $updatenational['enable']=0;
}

    Cards::where(array('nationalcompany'=>'national','api'=>1,'purchase'=>0))->update($updatenational);
    Company::where(array('kind'=>'national','api'=>1))->update($updatenationalcompany);
    

    session()->flash('success', __('site.updated_successfully'));
    return redirect()->route('dashboard.dubiorders.index');


}



public function enablenotapi(Request $request){
    
    $card=Cards::where(array('nationalcompany'=>'national','api'=>0))->orderBy('id','desc')->first();
if($card->enable ==0){
    $updatenotnational['enable']=1;
}else{
    $updatenotnational['enable']=0;
}

$company=Company::where(array('kind'=>'national','api'=>0))->orderBy('id','desc')->first();


if($company->enable ==0){
    $updatenationalcompany['enable']=1;
}else{
    $updatenationalcompany['enable']=0;
}



    Cards::where(array('nationalcompany'=>'national','api'=>0,'purchase'=>0))->update($updatenotnational);
    Company::where(array('kind'=>'national','api'=>0))->update($updatenationalcompany);

    session()->flash('success', __('site.updated_successfully'));
    return redirect()->route('dashboard.dubiorders.index');

}






public function enablenotlocalapi(Request $request){
    
    $card=Cards::where(array('nationalcompany'=>'local','api'=>0))->orderBy('id','desc')->first();
if($card->enable ==0){
    $updatenotnational['enable']=1;
}else{
    $updatenotnational['enable']=0;
}

$company=Company::where(array('kind'=>'local','api'=>0))->orderBy('id','desc')->first();


if($company->enable ==0){
    $updatenationalcompany['enable']=1;
}else{
    $updatenationalcompany['enable']=0;
}



    Cards::where(array('nationalcompany'=>'local','api'=>0,'purchase'=>0))->update($updatenotnational);
    Company::where(array('kind'=>'local','api'=>0))->update($updatenationalcompany);

    session()->flash('success', __('site.updated_successfully'));
    return redirect()->route('dashboard.dubiorders.index');

}





public function enableswaggerapi(Request $request){
    
    $card=Cards::where(array('api2'=>1))->orderBy('id','desc')->first();
if($card->enable ==0){
    $updatenotnational['enable']=1;
}else{
    $updatenotnational['enable']=0;
}

$company=Company::where(array('api2'=>1))->orderBy('id','desc')->first();


if($company->enable ==0){
    $updatenationalcompany['enable']=1;
}else{
    $updatenationalcompany['enable']=0;
}



    Cards::where(array('api2'=>1,'purchase'=>0))->update($updatenotnational);
    Company::where(array('api2'=>1))->update($updatenationalcompany);

    session()->flash('success', __('site.updated_successfully'));
    return redirect()->route('dashboard.dubiorders.index');

}


}
