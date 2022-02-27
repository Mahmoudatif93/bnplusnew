<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Order;
use App\Cards;
use App\Client;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;


class SadadController extends Controller
{
    use ApiResourceTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */





    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function verify(Request $request)
    {

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiYjk3NTMxYzJkNDkzMjdhMzAwNmRjN2NiOTc4NTRlODFjMWMwYzVkYWMzN2UyNzhhZjViMjYyNmFmMTE5YjVjMDMxZTQzNGU0NDE3ODFlYjkiLCJpYXQiOjE2NDQzNTU0NjEsIm5iZiI6MTY0NDM1NTQ2MSwiZXhwIjoxNzcwNTg1ODYxLCJzdWIiOiI3Iiwic2NvcGVzIjpbXX0.s0Yat6614IuR3jMJ0njo4-50DzfSjCd5tASebIDUyUP_O_wxFp4ed3av1Dari_xDv4OBn23wjoIURUOSkuVGSz84sLTbkWrv418CzZ-ygxXHeQoyZ-JUXGbk8-1A35SEJbBQdjPI8svlVs2UL_RTlQarZbDLDMtXH5heCsf3sf0nuK79zY_bhFFAZD882P3uViYnD-YcecRGFxjmxVz3vrShspwskg-kwM1sIrmLD95lRg7n7ZJItGCyaXDC27XJuVZUhmtCLA48iFBSBoTdk1NE_5pGiWn0UOzwvdbxWfKioQoeBrdP-wVJF9MDklahycPI4wN1ooKiSeeFL3xtBHSwjpk8GP1_y3UZZl99ANlR7j_jgKj8g_VH-w3m6I8dSTkbSvclBXY8joowgguOWkn4R3QV1hQtH4w-nf_14wV90hJE1O1NNEyQ3smidSSdQp0Qd_vlTqYOTgJPzlvkERxW-T2efJ9uM_TJFRPnXbSiLugC0AIIJw9GkBDAtUEhFKazYpRX4r45bOaOUKQtO65FFf_h40MBp-0DiTL6VIZX0X-jSxeAZ75ilBQVl7TUF_-zx5YsIN2xRLqgC97aqIe80rViUFARqWAQNQFCQFfe8Z7igpb0t4L49ZJ4JykktG03k53HZN4W2GZPOT2RdI2fgQVcytXza1VfXYmU2xo',
            'X-API-KEY' => '984adf4c-44e1-418f-829b'
        ])->post('https://api.plutus.ly/api/v1/transaction/sadadapi/verify', [
            'mobile_number' => $request->mobile_number,
            'birth_year' => $request->birth_year,
            'amount' => $request->amount
        ]);

        //return $response;
        $card = Cards::where(array('productId' => $request->card_id, 'avaliable' => 0, 'purchase' => 0,'enable'=>0))->orderBy('id', 'desc')->first();
        if (!empty($card)) {


            if (isset($response['error'])) {

                return $this->apiResponse4(false, $response['error']['message'], $response['error']['status']);
            } else {
                $process_id = $response['result']["process_id"];

                $request_data['card_id'] = $card->productId;


                $request_data['client_id'] = $request->client_id;
                $request_data['card_price'] = $request->amount;
                $request_data['client_name'] = $request->client_name;
                $request_data['client_number'] = $request->client_number;
                $request_data['process_id'] = "$process_id";
                $request_data['invoice_no'] = rand();
                $request_data['paymenttype'] = "سداد";
                // $order->invoice_no = rand();


                $order = Order::create($request_data);

                $dataa['avaliable'] = 1;
                Cards::where('productId', $order->card_id)->update($dataa);

                return $this->apiResponse5(true, $response['message'], $response['status'], $response['result'], $order->id);
            }
        } else {
            return $this->apiResponse4(false, 'No Avaliable Cards for this price', 400);
        }

        // dd($response );
    }



    public function confirm(Request $request)
    {


        $idfirst = $request->order_id;
        $orderfirst = Order::find($idfirst);
        if (!empty($orderfirst)) {

            $cards =   Cards::where('productId', $orderfirst->card_id)->first();

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








            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiYjk3NTMxYzJkNDkzMjdhMzAwNmRjN2NiOTc4NTRlODFjMWMwYzVkYWMzN2UyNzhhZjViMjYyNmFmMTE5YjVjMDMxZTQzNGU0NDE3ODFlYjkiLCJpYXQiOjE2NDQzNTU0NjEsIm5iZiI6MTY0NDM1NTQ2MSwiZXhwIjoxNzcwNTg1ODYxLCJzdWIiOiI3Iiwic2NvcGVzIjpbXX0.s0Yat6614IuR3jMJ0njo4-50DzfSjCd5tASebIDUyUP_O_wxFp4ed3av1Dari_xDv4OBn23wjoIURUOSkuVGSz84sLTbkWrv418CzZ-ygxXHeQoyZ-JUXGbk8-1A35SEJbBQdjPI8svlVs2UL_RTlQarZbDLDMtXH5heCsf3sf0nuK79zY_bhFFAZD882P3uViYnD-YcecRGFxjmxVz3vrShspwskg-kwM1sIrmLD95lRg7n7ZJItGCyaXDC27XJuVZUhmtCLA48iFBSBoTdk1NE_5pGiWn0UOzwvdbxWfKioQoeBrdP-wVJF9MDklahycPI4wN1ooKiSeeFL3xtBHSwjpk8GP1_y3UZZl99ANlR7j_jgKj8g_VH-w3m6I8dSTkbSvclBXY8joowgguOWkn4R3QV1hQtH4w-nf_14wV90hJE1O1NNEyQ3smidSSdQp0Qd_vlTqYOTgJPzlvkERxW-T2efJ9uM_TJFRPnXbSiLugC0AIIJw9GkBDAtUEhFKazYpRX4r45bOaOUKQtO65FFf_h40MBp-0DiTL6VIZX0X-jSxeAZ75ilBQVl7TUF_-zx5YsIN2xRLqgC97aqIe80rViUFARqWAQNQFCQFfe8Z7igpb0t4L49ZJ4JykktG03k53HZN4W2GZPOT2RdI2fgQVcytXza1VfXYmU2xo',
                'X-API-KEY' => '984adf4c-44e1-418f-829b'
            ])->post('https://api.plutus.ly/api/v1/transaction/sadadapi/confirm', [

                'process_id' => $orderfirst->process_id,
                'code' => $request->code,
                'amount' => $request->amount,
                'invoice_no' => $orderfirst->invoice_no,
                'customer_ip' => $request->customer_ip,

            ]);
            if (isset($response['error'])) {
                return $this->apiResponse4(false, $response['error']['message'], $response['error']['status']);
            } else {
                $id = $request->order_id;
                $order = Order::find($id);
                if (!empty($order)) {
                    $order->transaction_id = $response['result']['transaction_id'];
                    $order->paid = 'true';
                    $order->paymenttype = "سداد";

                    ////////////dubai api///////////////
                    $dubiapi =  Cards::where('productId', $order->card_id)->first();
                    $clientdata =  Client::where('id', $order->client_id)->first();

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
                            Cards::where('productId', $order->card_id)->update($updatecardprice);

                            $this->sendResetEmail($client->email, $this->decryptSerial($row['serialCode']), 'Your BNplus Code');
                        }

                        curl_close($curl);
                    }




                    ////////////////////////////////////////////////////////

                    if ($order->update()) {
                        $updatecard['purchase'] = 1;
                        $updatecard['avaliable'] = 1;
                        Cards::where('productId', $order->card_id)->update($updatecard);

                        $cardemail =  Cards::where('productId', $order->card_id)->first();
                        $client =  Client::where('id', $order->client_id)->first();

                        if ($dubiapi->api == 0) {
                            $this->sendResetEmail($client->email,  $cardemail->card_code, 'Your BNplus Code');
                        }

                        return $this->apiResponse5(true, $response['message'], $response['status'], $response['result']);
                    } else {
                        return response()->json(['status' => 'error']);
                    }
                } else {
                    return response()->json(['status' => 'error']);
                }
            }







        }
        else{
            return $this->apiResponse4(false, 'error in connection ', 400);

        }
        } else {
            return $this->apiResponse4(false, 'no Order for this order id', 400);
        }
    }

/*
    public function confirmnot($request)
    {


        $idfirst = $request->order_id;
        $orderfirst = Order::find($idfirst);
        if (!empty($orderfirst)) {

            $cards =   Cards::where('id', $orderfirst->card_id)->first();




            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiYjk3NTMxYzJkNDkzMjdhMzAwNmRjN2NiOTc4NTRlODFjMWMwYzVkYWMzN2UyNzhhZjViMjYyNmFmMTE5YjVjMDMxZTQzNGU0NDE3ODFlYjkiLCJpYXQiOjE2NDQzNTU0NjEsIm5iZiI6MTY0NDM1NTQ2MSwiZXhwIjoxNzcwNTg1ODYxLCJzdWIiOiI3Iiwic2NvcGVzIjpbXX0.s0Yat6614IuR3jMJ0njo4-50DzfSjCd5tASebIDUyUP_O_wxFp4ed3av1Dari_xDv4OBn23wjoIURUOSkuVGSz84sLTbkWrv418CzZ-ygxXHeQoyZ-JUXGbk8-1A35SEJbBQdjPI8svlVs2UL_RTlQarZbDLDMtXH5heCsf3sf0nuK79zY_bhFFAZD882P3uViYnD-YcecRGFxjmxVz3vrShspwskg-kwM1sIrmLD95lRg7n7ZJItGCyaXDC27XJuVZUhmtCLA48iFBSBoTdk1NE_5pGiWn0UOzwvdbxWfKioQoeBrdP-wVJF9MDklahycPI4wN1ooKiSeeFL3xtBHSwjpk8GP1_y3UZZl99ANlR7j_jgKj8g_VH-w3m6I8dSTkbSvclBXY8joowgguOWkn4R3QV1hQtH4w-nf_14wV90hJE1O1NNEyQ3smidSSdQp0Qd_vlTqYOTgJPzlvkERxW-T2efJ9uM_TJFRPnXbSiLugC0AIIJw9GkBDAtUEhFKazYpRX4r45bOaOUKQtO65FFf_h40MBp-0DiTL6VIZX0X-jSxeAZ75ilBQVl7TUF_-zx5YsIN2xRLqgC97aqIe80rViUFARqWAQNQFCQFfe8Z7igpb0t4L49ZJ4JykktG03k53HZN4W2GZPOT2RdI2fgQVcytXza1VfXYmU2xo',
                'X-API-KEY' => '984adf4c-44e1-418f-829b'
            ])->post('https://api.plutus.ly/api/v1/transaction/sadadapi/confirm', [

                'process_id' => $orderfirst->process_id,
                'code' => $request->code,
                'amount' => $request->amount,
                'invoice_no' => $orderfirst->invoice_no,
                'customer_ip' => $request->customer_ip,

            ]);
            if (isset($response['error'])) {
                return $this->apiResponse4(false, $response['error']['message'], $response['error']['status']);
            } else {
                $id = $request->order_id;
                $order = Order::find($id);
                if (!empty($order)) {
                    $order->transaction_id = $response['result']['transaction_id'];
                    $order->paid = 'true';
                    $order->paymenttype = "سداد";

                    ////////////dubai api///////////////
                    $dubiapi =  Cards::where('id', $order->card_id)->first();
                    $clientdata =  Client::where('id', $order->client_id)->first();

                    ////////////////////////////////////////////////////////

                    if ($order->update()) {
                        $updatecard['purchase'] = 1;
                        $updatecard['avaliable'] = 1;
                        Cards::where('id', $order->card_id)->update($updatecard);

                        $cardemail =  Cards::where('id', $order->card_id)->first();
                        $client =  Client::where('id', $order->client_id)->first();


                        $this->sendResetEmail($client->email,  $cardemail->card_code, 'Your BNplus Code');


                        return $this->apiResponse5(true, $response['message'], $response['status'], $response['result']);
                    } else {
                        return response()->json(['status' => 'error']);
                    }
                } else {
                    return response()->json(['status' => 'error']);
                }
            }
        } else {
            return $this->apiResponse4(false, 'no Order for this order id', 400);
        }
    }

    public function confirm(Request $request)
    {

        $idfirst = $request->order_id;
        $orderfirst = Order::find($idfirst);
        if (!empty($orderfirst)) {

            $cards =   Cards::where('id', $orderfirst->card_id)->first();

            if ($cards->api == 1) {

                $this->confirmapi($request);
            } else {
                $this->confirmnot($request);
            }
        } else {
            return $this->apiResponse4(false, 'no Order for this order id', 400);
        }
    }


*/
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
