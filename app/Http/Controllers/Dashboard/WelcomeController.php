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
class WelcomeController extends Controller
{
    public function index()
    {
        $orders = Order::count();
        $companies = Company::count();
        $cards = Cards::count();
        $clients = Client::count();
        
        return view('dashboard.welcome', compact('Companies','orders','cards','clients'));

    }//end of index

}//end of controller
