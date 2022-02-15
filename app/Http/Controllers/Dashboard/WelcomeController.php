<?php

namespace App\Http\Controllers\Dashboard;


use App\Client;
use App\Order;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class WelcomeController extends Controller
{
    public function index()
    {

        //dd(auth()->user()->first_name.' '.auth()->user()->last_name);
      /*  $categories_count = Category::count();
        $products_count = Product::count();
        $clients_count = Client::count();
        $users_count = User::whereRoleIs('admin')->count();*/
//extract(month from "created_at") as month


        /*$sales_data = Order::select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('MONTH(created_at) as month'),
            DB::raw('SUM(total_price) as sum')
        )->groupBy('month')->get();*/


      /*  $sales_data = Order::select(

            DB::raw('extract(YEAR from "created_at") as year'),
            DB::raw('extract(month from "created_at") as month'),
            DB::raw('SUM((total_price)) as sum')
        )->groupBy('created_at')->get();

        */


        return view('dashboard.welcome');

    }//end of index

}//end of controller
