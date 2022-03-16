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
use App\currancynational;
class currancyNationalController extends Controller
{


    public function index(Request $request)
    {           
        $curr=  currancynational::first();
        $Companies = currancynational::when($request->search, function ($q) use ($request) {

            return $q->where('amount', '%' . $request->search . '%');
        })->latest()->paginate(5);

        return view('dashboard.currancynational.index', compact('Companies'));
    } //end of index

    public function create()
    {
        return view('dashboard.currancynational.create');
    } //end of create

    public function store(Request $request)
    {
        $rules = [
            'amount' => 'required',
           
        ];

        $request->validate($rules);
        $request_data = $request->all();
    

        currancynational::create($request_data);
        session()->flash('success', __('site.added_successfully'));
        return redirect()->route('dashboard.currancynational.index');
    } //end of store

    public function edit($id)
    {
        $category = currancynational::where('id', $id)->first();
        return view('dashboard.currancynational.edit', compact('category'));
    } //end of edit

    public function update(Request $request, $id)
    {

        $oldallcardprices = array();
        $category = currancynational::where('id', $id)->first();

        
  
        
       foreach(Cards::where('api',1)->get() as $cards ){
        
        
         
            $newprice2['card_price']=$cards->old_price * $request->amount;
           // echo $newprice2['card_price'];echo "<br>";
            Cards::where(array('api'=>1,'id'=>$cards->id))->update($newprice2);
        }
     
       // return $oldallcardprices;


        $request_data = $request->except(['_token', '_method']);
        currancynational::where('id', $id)->update($request_data);
      
        session()->flash('success', __('site.updated_successfully'));
        return redirect()->route('dashboard.currancyswaggernational.index');
    } //end of update

    public function destroy($id)
    {
        $category = currancynational::where('id', $id)->first();
     
        currancynational::where('id', $id)->delete();
        session()->flash('success', __('site.deleted_successfully'));
        return redirect()->route('dashboard.Companies.index');
    } //end of destroy


 
 
}//end of controller
