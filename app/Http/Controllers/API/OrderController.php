<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Company;
use App\Order;
use App\Cards;
use App\Client;

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


        $cardscount = Cards::where(array('id' => $request->card_id,'avaliable' => 0, 'purchase' => 0))->count();

        if ($cardscount > 0) {
            $card = Cards::where(array('id' => $request->card_id,'avaliable' => 0, 'purchase' => 0))->orderBy('id', 'desc')->first();

            $request_data['card_id'] = $card->id;
            $request_data['client_id'] = $request->client_id;
            $request_data['card_price'] = $request->card_price;
            $request_data['client_name'] = $request->client_name;
            $request_data['client_number'] = $request->client_number;
            $order = Order::create($request_data);

            if ($order) {
                $dataa['avaliable'] = 1;
                Cards::where('id', $order->card_id)->update($dataa);

                $message = "card reserved ";
                return $this->apiResponse6($cardscount - 1, $order->id, $message, 200);
            } else {

                return $this->apiResponse6(null, null, 'error to Reserve Order', 404);
            }
        } else {
            $message = "No Cards Avaliable For this Price";
            return $this->apiResponse6($cardscount, null, $message, 404);
        }
    }

    public function clientorder(Request $request)
    {

        $order = Order::where(array('client_id'=>$request->clientid,'paid'=>true))->with('cards')->get();


        if (count($order) > 0) {
            return $this->apiResponse($order, 'You have orders', 200);
        } else {
            return $this->apiResponse($order, 'No orders Avaliable', 400);
        }
    }


    public function finalorder(Request $request)
    {
        $id = $request->order_id;
        $order = Order::find($id);
        if (!empty($order)) {
            $order->transaction_id = $request->transaction_id;
            $order->paid = 'true';
            $order->paymenttype="معاملات";

            //  dd($request->title);


            ////////////dubai api///////////////
          $dubiapi=  Cards::where('id', $order->card_id)->first();
          $clientdata=  Client::where('id', $order->client_id)->first();
            if($dubiapi->api==1){
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
                    'deviceId' => '4d2ec47930a1fe0706836fdd1157a8c320dfc962aa6d0b0df2f4dda40a27b2ba',
                    'email' => 'sales@bn-plus.ly',
                    'password' => '149e7a5dcc2b1946ebf09f6c7684ab2c',
                    'securityCode' => '4d2ec47930a1fe0706836fdd1157a8c36bd079faa0810ff7562c924a23c3f415',
                    'langId' => '1',
                    'productId' => $order->card_id,
                    'referenceId' => $refrenceid,
                    'time' => time(),
                    'hash'=>$this->generateHash( $clientdata->phone, $clientdata->email),

                ),

            ));

            $createorder = curl_exec($curl);
            curl_close($curl);
        }






            /////////////
            if ($order->update()) {
                $updatecard['purchase'] = 1;
                $updatecard['avaliable'] = 1;
                Cards::where('id', $order->card_id)->update($updatecard);

                $cardemail=  Cards::where('id', $order->card_id)->first();
                $client=  Client::where('id', $order->client_id)->first();
                $this->sendResetEmail( $client->email,  $cardemail->card_code, 'Your BNplus Code');


                return response()->json(['status' => 'success']);
            } else {
                return response()->json(['status' => 'error']);
            }
        } else {
            return response()->json(['status' => 'error']);
        }
    }


    
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

    function generateHash($phone,$mail){
        $email = strtolower($mail);
        $key = hash('sha256', 't-3zafRa');   
        $time=time();
        return hash('sha256',$time.$email.$phone.$key);
      }

}
