<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Company;
use App\Cards;
use App\Order;
use App\Client;
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        
        $orders = Order::count();
        $companies = Company::count();
        $cards = Cards::where(array('avaliable' => 0, 'purchase' => 0))->count();
        $clients = Client::count();
        
        return view('home', compact('companies','orders','cards','clients'));
    }
}
