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
use App\Currency;
class currancyController extends Controller
{


    public function index(Request $request)
    {           
        $curr=  Currency::first();
        $Companies = Currency::when($request->search, function ($q) use ($request) {

            return $q->where('amount', '%' . $request->search . '%');
        })->latest()->paginate(5);

        return view('dashboard.currancy.index', compact('Companies'));
    } //end of index

    public function create()
    {
        return view('dashboard.currancy.create');
    } //end of create

    public function store(Request $request)
    {
        $rules = [
            'amount' => 'required',
           
        ];

        $request->validate($rules);
        $request_data = $request->all();
    

        Currency::create($request_data);
        session()->flash('success', __('site.added_successfully'));
        return redirect()->route('dashboard.currancy.index');
    } //end of store

    public function edit($id)
    {
        $category = Currency::where('id', $id)->first();
        return view('dashboard.currancy.edit', compact('category'));
    } //end of edit

    public function update(Request $request, $id)
    {

        $oldallcardprices = array();
        $category = Currency::where('id', $id)->first();

        
    


        
        
       foreach(Cards::where('api',1)->get() as $cards ){
            $newprice['card_price']=$cards->card_price /$category->amount;
        //  echo $newprice['card_price']; echo "<br>";
           Cards::where('api',1)->update($newprice);
           array_push($oldallcardprices, $newprice['card_price']);
            $newprice2['card_price']=$cards->card_price * $request->amount;
           // echo $newprice2['card_price'];echo "<br>";
           // Cards::where('api',1)->update($newprice2);
        }
     
       // return $oldallcardprices;


        $request_data = $request->except(['_token', '_method']);
       Currency::where('id', $id)->update($request_data);
      
        session()->flash('success', __('site.updated_successfully'));
        return redirect()->route('dashboard.currancy.index');
    } //end of update

    public function destroy($id)
    {
        $category = Currency::where('id', $id)->first();
     
        Currency::where('id', $id)->delete();
        session()->flash('success', __('site.deleted_successfully'));
        return redirect()->route('dashboard.Companies.index');
    } //end of destroy


 
 
}//end of controller
