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
        
        $Companies = Company::where(array('enable'=>1,'api'=>1))->when($request->search, function ($q) use ($request) {

            return $q->where('name','like', '%' .  $request->search . '%')
                ->orWhere('kind', 'like', '%' . $request->search . '%');
        })->latest()->paginate(5);
        
        return view('dashboard.dubioff.index', compact('Companies'));
        
    }

    public function dubioff($id)
    {
        dd($id);
    }

    public function disabledubioff($id)
    {
        $updatenational['enable']=1;
        Cards::where(array('company_id'=>$id))->update($updatenational);
        Company::where(array('id'=>$id))->update($updatenational);
        session()->flash('success', __('site.updated_successfully'));
        return redirect()->route('dashboard.dubiorders.index');
    }
    
    public function enabledubioff($id)
    {
        $updatenational['enable']=0;
        Cards::where(array('company_id'=>$id))->update($updatenational);
        Company::where(array('id'=>$id))->update($updatenational);
        return redirect()->route('dashboard.dubioff.index');
    }

}
