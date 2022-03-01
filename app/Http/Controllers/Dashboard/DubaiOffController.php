<?php

namespace App\Http\Controllers\Dashboard;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Company;
use App\Cards;
class DubaiOffController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        
        $Companies = Company::where(array('api'=>1))->when($request->search, function ($q) use ($request) {

            return $q->where('name','like', '%' .  $request->search . '%')
                ->orWhere('kind', 'like', '%' . $request->search . '%');
        })->latest()->paginate(5);
        
        return view('dashboard.dubioff.index', compact('Companies'));
        
    }

    public function show($id)
    {
       
     $cards=   Cards::where(array('company_id'=>$id))->get();
     return view('dashboard.dubioff.dubaiorderdetails', compact('cards'));



    }

    public function disabledubioff($id)
    {
      
        $updatenational['enable']=1;
        Cards::where(array('company_id'=>$id))->update($updatenational);
        Company::where(array('id'=>$id))->update($updatenational);
        session()->flash('success', __('site.updated_successfully'));
        return redirect()->back();
    }
    
    public function enabledubioff($id)
    {
  
        $updatenational['enable']=0;
        Cards::where(array('company_id'=>$id))->update($updatenational);
        Company::where(array('id'=>$id))->update($updatenational);
        return redirect()->back();
    }


    public function dubidisablecard($id)
    {
        
        $updatenational['enable']=1;
        Cards::where(array('id'=>$id))->update($updatenational);
       
        session()->flash('success', __('site.updated_successfully'));
        return redirect()->back();
    }
    
    public function dubienablecard($id)
    {
        
        $updatenational['enable']=0;
        Cards::where(array('id'=>$id))->update($updatenational);
      
        return redirect()->back();
    }

}
