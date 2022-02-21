<?php

namespace App\Http\Controllers\Dashboard;

use App\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Company;
class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::whereHas('client', function ($q) use ($request) {

            return $q->where('name', 'like', '%' . $request->search . '%')
            ->orWhere('card_price', 'like', '%' . $request->search . '%');

        })->orderBy('id','desc')->paginate(5);
//dd($orders);
        return view('dashboard.orders.index', compact('orders'));

    }//end of index

    public function products(Order $order)
    {
        $products = $order->cards;
     
        /*foreach ($products as $product){
            $company=  \App\Company::where(['id' => $product->company_id])->first();
             }*/
        
  

        return view('dashboard.orders._products', compact('order', 'products'));

    }//end of products
    
    public function destroy(Order $order)
    {
   

        $order->delete();
        session()->flash('success', __('site.deleted_successfully'));
        return redirect()->route('dashboard.orders.index');
    
    }//end of order

}//end of controller
