<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Company;
use App\Cards;
use App\Order;
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

        
        $cardscount = Cards::where(array('card_price' => $request->card_price, 'avaliable' => 0, 'purchase' => 0))->count();

        if ($cardscount > 0) {
            $card = Cards::where(array('avaliable' => 0, 'purchase' => 0, 'card_price' => $request->card_price))->orderBy('id', 'desc')->first();

            $request_data['card_id'] = $card->id;
            $request_data['client_id'] = $request->client_id;
            $request_data['card_price'] = $request->card_price;
            $request_data['client_name'] = $request->client_name;
            $request_data['client_number'] = $request->client_number;
            $order = Order::create($request_data);

            if($order){
               $dataa['avaliable']=1;
               Cards:: where('id', $order->card_id)->update($dataa);
            
               $message="card reserved ";
               return $this->apiResponse6($cardscount -1, $order->id,$message, 200);
            }else{
            
              return $this->apiResponse6(null, null,'error to Reserve Order', 404);
            }

        } else {
            $message = "No Cards Avaliable For this Price";
            return $this->apiResponse6($cardscount, null,$message, 404);
            
        }

        
    }

    public function clientorder(Request $request)
    {
        
        $order= Order::where('client_id', $request->clientid)->with('cards')->get();   

        
        if(count($order) >0){
            return $this->apiResponse($order, 'You have orders',200);
        }else{
            return $this->apiResponse($order, 'No orders Avaliable',400);
        }
    }

    
    public function finalorder(Request $request)
    {
        $id=$request->order_id;
    $order=Order::find($id);
    if(!empty($order)){
        $order->transaction_id=$request->transaction_id;
        $order->paid='true';

    //  dd($request->title);
        if($order->update()){
            $updatecard['purchase']=1;
            Cards:: where('id', $order->card_id)->update( $updatecard);



            return response()->json(['status'=>'success']);
        }else{
            return response()->json(['status'=>'error']);
        }
    }else{
        return response()->json(['status'=>'error']);
    }
 
}

}
