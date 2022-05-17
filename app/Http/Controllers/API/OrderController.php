<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Company;
use App\Order;
use App\Cards;
use App\Client;
use App\Anaiscodes;
use App\cards_anais;
use App\Order_anais;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use PDF2;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class OrderController extends Controller
{

    use ApiResourceTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */







    public function reserveorder(Request $request)
    {



        $cardscount = Cards::where(array('id' => $request->card_id, 'avaliable' => 0, 'purchase' => 0, 'enable' => 0))->count();
        $card = Cards::where(array('id' => $request->card_id, 'avaliable' => 0, 'purchase' => 0, 'enable' => 0))->orderBy('id', 'desc')->first();

        if ($cardscount > 0) {

            if ($card->api2 == 1) {
    
    
                $uri = 'https://identity.anis.ly/connect/token';
                $params = array(
                    'grant_type' => 'user_credentials',
                    'client_id' => 'bn-plus',
                    'client_secret' => '3U8F3U9C9IM39VJ39FUCLWLC872MMXOW8K2STWI28ZJD3ERF',
                    'password' => 'P@ssw0rd1988',
                    'email' => 'info@bn-plus.ly',
                );
                $response = Http::asForm()->withHeaders([])->post($uri, $params);
                $token = $response->json()['access_token'];
                $token_type = $response->json()['token_type'];
                $alltoken = $response->json()['token_type'] . ' ' . $response->json()['access_token'];

                $compurlcheck = 'https://gateway.anis.ly/api/consumers/v1/categories/cards/' . $card->api2id . '';

                $cardschek = Http::withHeaders([
                    'Accept' => 'application/json',
                    'Authorization' => $alltoken,

                ])->get($compurlcheck);


                if (isset($cardschek->json()['data'])) {
              
                        if ($cardschek->json()['data']['inStock'] == false) {
                            $updatecard['purchase'] = 1;
                            $updatecard['avaliable'] = 1;
                            Cards::where('id',$card->id)->update($updatecard);
                            return $this->apiResponse6(null, null, 'error to Reserve Order', 404);
                        }else{

                            $request_data['card_id'] = $card->id;
                            $request_data['client_id'] = $request->client_id;
                            $request_data['card_price'] = $request->card_price;
                            $request_data['client_name'] = $request->client_name;
                            $request_data['client_number'] = $request->client_number;
                            $request_data['paymenttype'] = "معاملات";
                
                            $order = Order::create($request_data);
                
                            if ($order) {
                           
                                $message = "card reserved ";
                                return $this->apiResponse6($cardscount - 1, $order->id, $message, 200);
                            } else {
                
                                return $this->apiResponse6(null, null, 'error to Reserve Order', 404);
                            }

                        }
                    
                }

            
    
              


            }else{



                if ($card->api == 1) {

                    $request_data['card_id'] = $card->id;
                } else {
                    $request_data['card_id'] = $card->id;
                }
    
    
            
    
                $request_data['client_id'] = $request->client_id;
                $request_data['card_price'] = $request->card_price;
                $request_data['client_name'] = $request->client_name;
                $request_data['client_number'] = $request->client_number;
                $request_data['paymenttype'] = "معاملات";
    
                $order = Order::create($request_data);
    
                if ($order) {
                    if ($card->api2 != 1) {
                        $dataa['avaliable'] = 1;
                        Cards::where('id', $order->card_id)->update($dataa);
                    }
    
                    $message = "card reserved ";
                    return $this->apiResponse6($cardscount - 1, $order->id, $message, 200);
                } else {
    
                    return $this->apiResponse6(null, null, 'error to Reserve Order', 404);
                }

                
            }



           
        } else {
            $message = "No Cards Avaliable For this Price";
            return $this->apiResponse6($cardscount, null, $message, 404);
        }
    }

    public function clientorder(Request $request)
    {

        $order = Order_anais::where(array('client_id' => $request->clientid, 'paid' => "true"))->with('cards')->get();
        
         
       /* foreach( $orders as $row){
             $carsss=Cards::where('id',$row->card_id)->get();
             if(!empty( $carsss)){
                 foreach( $carsss as $rowca){
                     
                       if($rowca->api2==1){
                     
                  $order = Order_anais::where(array('client_id' => $request->clientid, 'paid' => "true"))->with('cards')->get();
                  
                  
             }else{
             
                 $order = Order::where(array('client_id' => $request->clientid, 'paid' => "true"))->with('cards')->get(); 
                 
             }
              } 
             }
              
           
             
        }
      */
        
        


        if (count($order) > 0) {
            return $this->apiResponse($order, 'You have orders', 200);
        } else {
            return $this->apiResponse($order, 'No orders Avaliable', 200);
        }
    }




    public function finalorder(Request $request)
    {
  

      $id = $request->order_id;
        $order = Order::find($id);

        $dubiapi =  Cards::where('id', $order->card_id)->first();
        if (!empty($order)) {


            $is_expired = $order->created_at->addMinutes(5);
            if ($is_expired < \Carbon\Carbon::now()) {

                return response()->json(['status' => 'error']);
            } else {
                $order->transaction_id = $request->transaction_id;
                $order->paid = 'true';
                $order->paymenttype = "معاملات";

                $allcompanyid = array();
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => "https://taxes.like4app.com/online/check_balance/",
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
                        'langId' => '1'
                    ),

                ));

                $balancenational = curl_exec($curl);

                if (isset($balancenational) && !empty($balancenational) && $balancenational != 'error code: 1020') {

                    ////////////dubai api///////////////



                    $dubiapi =  Cards::where('id', $order->card_id)->first();
                    $clientdata =  Client::where('id', $order->client_id)->first();
                    if ($dubiapi->api != 1 || $dubiapi->api2 != 1) {

                        $cardsanaia = Cards::where('id', $order->card_id)->first();
                         /* $Anaiscards['id'] = $order->card_id;
                        $Anaiscards['order_id'] = $order->id;
                        $Anaiscards['card_name'] = $cardsanaia->card_name;
                        $Anaiscards['company_id'] = $cardsanaia->company_id;
                        $Anaiscards['api'] = $cardsanaia->api;
                        $Anaiscards['card_price'] = $cardsanaia->card_price;
                        $Anaiscards['card_code'] =  $cardsanaia->card_code;
                        $Anaiscards['amounts'] = $cardsanaia->amounts;

                        $Anaiscards['avaliable'] = $cardsanaia->avaliable;
                        $Anaiscards['purchase'] = $cardsanaia->purchase;
                        $Anaiscards['card_image'] = $cardsanaia->card_image;
                        $Anaiscards['nationalcompany'] = $cardsanaia->nationalcompany;
                        $Anaiscards['productId'] = $cardsanaia->productId;
                        $Anaiscards['enable'] = $cardsanaia->enable;
                        $Anaiscards['api2'] = $cardsanaia->api2;
                        $Anaiscards['api2id'] = $cardsanaia->api2id;
                        cards_anais::create($Anaiscards);
                        
                        */
                        
                        
                        
                        
                                  $cardsanaia = Cards::where('id', $order->card_id)->first();
                            $Anaiscards['id'] = $cardsanaia->id;
                           $itemcomp = cards_anais::firstOrNew(array('order_id' => $order->id));
                           $itemcomp->id = $cardsanaia->id;
                           $itemcomp->order_id =  $order->id;
                           $itemcomp->card_name = $cardsanaia->card_name;
                           $itemcomp->company_id = $cardsanaia->company_id;
                           $itemcomp->api =  $cardsanaia->api;
                           $itemcomp->card_price =  $cardsanaia->card_price;
                           $itemcomp->card_code =$cardsanaia->card_code;
                           $itemcomp->amounts = $cardsanaia->amounts;
                           $itemcomp->avaliable = $cardsanaia->avaliable;
                           $itemcomp->purchase =  $cardsanaia->purchase;
                           $itemcomp->card_image = $cardsanaia->card_image;
                           $itemcomp->nationalcompany = $cardsanaia->nationalcompany;
                           $itemcomp->productId = $cardsanaia->productId;
                           $itemcomp->enable = $cardsanaia->enable;
                           $itemcomp->api2 = $cardsanaia->api2;
                           $itemcomp->api2id = $cardsanaia->api2id;
                    
                     
                           $itemcomp->save();
                           
                    }


                    if ($dubiapi->api == 1) {
                        $client =  Client::where('id', $order->client_id)->first();
                        $curl = curl_init();
                        $refrenceid = "Merchant_" . rand();
                        curl_setopt_array($curl, array(
                            CURLOPT_URL => "https://taxes.like4app.com/online/create_order",
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
                                'productId' => $order->card_id,
                                'referenceId' => $refrenceid,
                                'time' => time(),
                                'hash' => $this->generateHash($clientdata->phone, $clientdata->email),

                            ),

                        ));

                        $createorder = curl_exec($curl);

                        $json = json_decode($createorder, true);
                        //  return $json['serials'];

                        foreach ($json['serials'] as $row) {
                            //  return $row['serialCode'];
                            $updatecardprice['card_code'] =  $this->decryptSerial($row['serialCode']);
                            Cards::where('id', $order->card_id)->update($updatecardprice);
                            //  $this->sendResetEmail($client->email, $this->decryptSerial($row['serialCode']), 'Your BNplus Code');


                            $cardsanaia = Cards::where('id', $order->card_id)->first();
                           /*   $Anaiscards['id'] = $order->card_id;
                            $Anaiscards['order_id'] = $order->id;
                            $Anaiscards['card_name'] = $cardsanaia->card_name;
                            $Anaiscards['company_id'] = $cardsanaia->company_id;
                            $Anaiscards['api'] = $cardsanaia->api;
                            $Anaiscards['card_price'] = $cardsanaia->card_price;
                            $Anaiscards['card_code'] = $this->decryptSerial($row['serialCode']);
                            $Anaiscards['amounts'] = $cardsanaia->amounts;

                            $Anaiscards['avaliable'] = $cardsanaia->avaliable;
                            $Anaiscards['purchase'] = $cardsanaia->purchase;
                            $Anaiscards['card_image'] = $cardsanaia->card_image;
                            $Anaiscards['nationalcompany'] = $cardsanaia->nationalcompany;
                            $Anaiscards['productId'] = $cardsanaia->productId;
                            $Anaiscards['enable'] = $cardsanaia->enable;
                            $Anaiscards['api2'] = $cardsanaia->api2;
                            $Anaiscards['api2id'] = $cardsanaia->api2id;
                            cards_anais::create($Anaiscards);*/
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            
                                  $cardsanaia = Cards::where('id', $order->card_id)->first();
                            $Anaiscards['id'] = $cardsanaia->id;
                         //  $itemcomp = cards_anais::firstOrNew(array('id' => $cardsanaia->id));
                           $itemcomp = cards_anais::firstOrNew(array('order_id' => $order->id));
                           $itemcomp->id = $cardsanaia->id;
                           $itemcomp->order_id =  $order->id;
                           $itemcomp->card_name = $cardsanaia->card_name;
                           $itemcomp->company_id = $cardsanaia->company_id;
                           $itemcomp->api =  $cardsanaia->api;
                           $itemcomp->card_price =  $cardsanaia->card_price;
                           $itemcomp->card_code =$this->decryptSerial($row['serialCode']);
                           $itemcomp->amounts = $cardsanaia->amounts;
                           $itemcomp->avaliable = $cardsanaia->avaliable;
                           $itemcomp->purchase =  $cardsanaia->purchase;
                           $itemcomp->card_image = $cardsanaia->card_image;
                           $itemcomp->nationalcompany = $cardsanaia->nationalcompany;
                           $itemcomp->productId = $cardsanaia->productId;
                           $itemcomp->enable = $cardsanaia->enable;
                           $itemcomp->api2 = $cardsanaia->api2;
                           $itemcomp->api2id = $cardsanaia->api2id;
                    
                     
                           $itemcomp->save();
                           
                           
                        }


                        curl_close($curl);
                    }

                    if ($dubiapi->api2 == 1) {



                        $client =  Client::where('id', $order->client_id)->first();
                        //   rand();

                        $uri = 'https://identity.anis.ly/connect/token';
                        $params = array(
                            'grant_type' => 'user_credentials',
                            'client_id' => 'bn-plus',
                            'client_secret' => '3U8F3U9C9IM39VJ39FUCLWLC872MMXOW8K2STWI28ZJD3ERF',
                            'password' => 'P@ssw0rd1988',
                            'email' => 'info@bn-plus.ly',
                        );
                        $response = Http::asForm()->withHeaders([])->post($uri, $params);
                        $token = $response->json()['access_token'];
                        $token_type = $response->json()['token_type'];
                        $alltoken = $response->json()['token_type'] . ' ' . $response->json()['access_token'];

                        $orders = Http::withHeaders([
                            'Accept' => 'application/json',
                            'Authorization' => $alltoken,

                        ])->post(
                            'https://gateway.anis.ly/api/consumers/v1/order',
                            [


                                'walletId' => 'E1521F1F-C592-42F3-7A1A-08D9F31F6661',
                                'cardId' => $dubiapi->api2id,
                                'pinNumber' => '1988',
                                'orderId' => rand(),
                                'quantity' => 1,
                                'TotalValue' => $dubiapi->old_price,

                            ]

                        );
                      
                        if (isset($orders->json()['data'])) {
                          //  dd($orders->json()['data']);
                            foreach ($orders->json()['data']['cards'] as $cardddds) {
                                $updatecardprssice['card_code'] = $cardddds['secretNumber'];
                                Cards::where('id', $order->card_id)->update($updatecardprssice);


                                $Anaiscodes['client_id'] = $order->client_id;
                                $Anaiscodes['card_code'] = $cardddds['secretNumber'];
                                $Anaiscodes['order_id'] = $order->id;


                                Anaiscodes::create($Anaiscodes);


                                  /*$cardsanaia= Cards::where('id',$order->card_id)->first();
                                    $Anaiscards['id'] = $order->card_id;
                                   $Anaiscards['order_id'] = $order->id;
                                  $Anaiscards['card_name'] = $cardsanaia->card_name;
                                  $Anaiscards['company_id'] = $cardsanaia->company_id;
                                  $Anaiscards['api'] = $cardsanaia->api;
                                  $Anaiscards['card_price'] = $cardsanaia->card_price;
                                  $Anaiscards['card_code'] = $cardddds['secretNumber'];
                                  $Anaiscards['amounts'] = $cardsanaia->amounts;
                                  $Anaiscards['avaliable'] = $cardsanaia->avaliable;
                                  $Anaiscards['purchase'] = $cardsanaia->purchase;
                                  $Anaiscards['card_image'] = $cardsanaia->card_image;
                                  $Anaiscards['nationalcompany'] = $cardsanaia->nationalcompany;
                                  $Anaiscards['productId'] = $cardsanaia->productId;
                                  $Anaiscards['enable'] = $cardsanaia->enable;
                                  $Anaiscards['api2'] = $cardsanaia->api2;
                                  $Anaiscards['api2id'] = $cardsanaia->api2id;
                                  cards_anais::create($Anaiscards);*/
                                  
                                  
                                          $cardsanaia = Cards::where('id', $order->card_id)->first();
                            $Anaiscards['id'] = $cardsanaia->id;
                         //  $itemcomp = cards_anais::firstOrNew(array('id' => $cardsanaia->id));
                           $itemcomp = cards_anais::firstOrNew(array('order_id' => $order->id));

                           $itemcomp->id = $cardsanaia->id;
                           $itemcomp->order_id =  $order->id;
                           $itemcomp->card_name = $cardsanaia->card_name;
                           $itemcomp->company_id = $cardsanaia->company_id;
                           $itemcomp->api =  $cardsanaia->api;
                           $itemcomp->card_price =  $cardsanaia->card_price;
                           $itemcomp->card_code =$cardddds['secretNumber'];
                           $itemcomp->amounts = $cardsanaia->amounts;
                           $itemcomp->avaliable = $cardsanaia->avaliable;
                           $itemcomp->purchase =  $cardsanaia->purchase;
                           $itemcomp->card_image = $cardsanaia->card_image;
                           $itemcomp->nationalcompany = $cardsanaia->nationalcompany;
                           $itemcomp->productId = $cardsanaia->productId;
                           $itemcomp->enable = $cardsanaia->enable;
                           $itemcomp->api2 = $cardsanaia->api2;
                           $itemcomp->api2id = $cardsanaia->api2id;
                    
                     
                           $itemcomp->save();
                               
                               
                            }
                        }



                        $compurlcheck = 'https://gateway.anis.ly/api/consumers/v1/categories/cards/' . $dubiapi->api2id . '';

                        $cardschek = Http::withHeaders([
                            'Accept' => 'application/json',
                            'Authorization' => $alltoken,

                        ])->get($compurlcheck);


                        if (isset($cardschek->json()['data'])) {

                            if ($cardschek->json()['data']['inStock'] == false) {
                                $updatecard['purchase'] = 1;
                                $updatecard['avaliable'] = 1;
                                Cards::where('id', $order->card_id)->update($updatecard);
                            }
                        }
                    }





                    /////////////


                    if ($order->update()) {
                        if ($dubiapi->api2 == 0) {
                            $updatecard['purchase'] = 1;
                            $updatecard['avaliable'] = 1;
                            Cards::where('id', $order->card_id)->update($updatecard);
                        }
                        $cardemail =  Cards::where('id', $order->card_id)->first();
                        $client =  Client::where('id', $order->client_id)->first();
                        if ($dubiapi->api == 0) {
                            //$this->sendResetEmail($client->email,  $cardemail->card_code, 'Your BNplus Code');
                        }

                        return response()->json(['status' => 'success']);
                    } else {
                        return response()->json(['status' => 'error']);
                    }
                } else {


                    return response()->json(['status' => 'error']);
                }
            }
        } else {
            return response()->json(['status' => 'error']);
        }

    }
    /*
    public function finalordernotdubai($request)
    {



        $id = $request->order_id;
        $order = Order::find($id);
        if (!empty($order)) {
            
            $order->transaction_id = $request->transaction_id;
            $order->paid = 'true';
            $order->paymenttype = "معاملات";

            /////////////
            if ($order->update()) {
                $updatecard['purchase'] = 1;
                $updatecard['avaliable'] = 1;
                Cards::where('id', $order->card_id)->update($updatecard);

                $cardemail =  Cards::where('id', $order->card_id)->first();
                $client =  Client::where('id', $order->client_id)->first();

                $this->sendResetEmail($client->email,  $cardemail->card_code, 'Your BNplus Code');


                return response()->json(['status' => 'success']);
            } else {
                return response()->json(['status' => 'error']);
            }
        } else {
            return response()->json(['status' => 'error']);
        }
    }





    public function finalorder(Request $request)
    {


        $id = $request->order_id;
        $order = Order::find($id);
        if (!empty($order)) {
            $cards =   Cards::where('id', $order->card_id)->first();

            if ($cards->api == 1) {

                $this->finalorderdubai($request);
            } else {
                
                $this->finalordernotdubai($request);
            }
        } else {
            return response()->json(['status' => 'error']);
        }
    }*/



    public function sendResetEmail($user, $content, $subject)
    {

        $send =   Mail::send(
            'dashboard.Contacts.content',
            ['user' => $user, 'content' => $content, 'subject' => $subject],
            function ($message) use ($user, $subject) {
                $message->to($user);
                $message->subject("$subject");
            }
        );
    }

    function generateHash($phone, $mail)
    {
        $email = strtolower($mail);
        $key = hash('sha256', 't-3zafRa');
        $time = time();
        return hash('sha256', $time . $email . $phone . $key);
    }


    function decryptSerial($encrypted_txt)
    {
        $secret_key = 't-3zafRa';
        $secret_iv = 'St@cE4eZ';
        $encrypt_method = 'AES-256-CBC';
        $key = hash('sha256', $secret_key);

        //iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning          
        $iv = substr(hash('sha256', $secret_iv), 0, 16);

        return openssl_decrypt(base64_decode($encrypted_txt), $encrypt_method, $key, 0, $iv);
    }
}
