<?php

namespace App\Http\Controllers\Dashboard;

use App\Company;
use App\Cards;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use App\Imports\CardImport;
use Maatwebsite\Excel\Facades\Excel;
use DB;

use App\Order;
use App\Carbon\Carbon;
class CardsController extends Controller
{
    public function index(Request $request)
    {
    //  $allorders=Cards::where('id',1)->update(array('avaliable'=>1));
       // $cards=Cards::all();
       // $allorders=Order::all()->unique('card_id');
        //dd($allorders);
        /*if(!empty($allorders)){
         foreach($allorders as $row){
             
             $is_expired = $row->created_at->addMinutes(10);
             if($is_expired < \Carbon\Carbon::now()){


         Cards::where('id',$row->card_id)->update(array('avaliable'=>0));
               
          
             }
         }
        }*/
        //dd($cards);
        $Cards = Cards::where(array('avaliable' => 0, 'purchase' => 0))->when($request->search, function ($q) use ($request) {

            return $q->where('card_code', '%' . $request->search . '%')
            ->orWhere('card_name', 'like', '%' . $request->search . '%')
            ->orWhere('card_price', 'like', '%' . $request->search . '%');

        })->latest()->paginate(5);
        $Companies = Company::all();
    //dd($Cards);
        return view('dashboard.Cards.index', compact('Cards','Companies'));

    }//end of index

    public function create()
    {
        $Companies = Company::all();
        return view('dashboard.Cards.create', compact('Companies'));

    }//end of create

    public function store(Request $request)
    {


        if($request->nationalcompany=="InternationalAPI"){
            
      
            $rules = [
                'company_id' => 'required',
                
              
            ];
           // $request_national = $request->all();
            $request_national['nationalcompany']='InternationalAPI';
            $request_national['company_id']= $request->company_id;
            Cards::create($request_national);

        }else{
           
         

          
        
        if ( $request->hasFile('file')) {
         $allcarcode=array();
            $cardcode = Excel::toArray(new CardImport, request()->file('file')); 
      
                $rules = [
                    'company_id' => 'required',
                    'card_price' => 'required',
                    'card_name' => 'required',
                  //  'card_code' => 'required',
                    //'amounts' => 'required',
                ];
                
                $request->validate($rules);
                $request_data = $request->all();
       
                for($i=0;$i<count($cardcode);$i++){
                 //   echo $i;
                   // echo"<br>";
                    $allcarcode= $cardcode[$i];
                    
                    for($j=0;$j<count($allcarcode);$j++){
                        $request_data['card_code']= $allcarcode[$j]['card_code'];
       //  print( $request_data['card_code']);
              if($request->offer=="on"){
                    $request_data['offer']=1;
                }else{
                    $request_data['offer']=0;
                }
        
                if ($request->card_image) {
        
                    Image::make($request->card_image)
                        ->resize(300, null, function ($constraint) {
                            $constraint->aspectRatio();
                        })
                        ->save(public_path('uploads/cards/' . $request->card_image->hashName()));
        
                    $request_data['card_image'] = 'https://bn-plus.ly/BNplus/public/uploads/cards/'.$request->card_image->hashName();
        
                }//end of if
               // print_r($request_data);
              Cards::create($request_data);
             /* echo $i;
              echo "<br>";
              echo $j;
              echo "<br>";
              print_r($request_data);*/

            }}


//dd();
               

        }if($request->card_code[0] !=null){


            $rules = [
                'company_id' => 'required',
                'card_price' => 'required',
                'card_code' => 'required',
                'card_name' => 'required',
            ];
            
            $request->validate($rules);
            $request_data = $request->all();
            if($request->kind=="national" && $request->nationalcompany=="national"){
                   
                $request_data['nationalcompany']='national';
           
           
    
            }else{
             
                $request_data['nationalcompany']='local';
               
            }
       
            for($i=0;$i<count($request->card_code);$i++){
                $request_data['card_code'] = $request->card_code[$i];
        /*    if($request->offer=="on"){
                $request_data['offer']=1;
            }else{
                $request_data['offer']=0;
            }
    */
            if ($request->card_image) {
    
                Image::make($request->card_image)
                    ->resize(300, null, function ($constraint) {
                        $constraint->aspectRatio();
                    })
                    ->save(public_path('uploads/cards/' . $request->card_image->hashName()));
    
             $request_data['card_image'] = 'https://bn-plus.ly/BNplus/public/uploads/cards/'.$request->card_image->hashName();
    
            }//end of if
    
            Cards::create($request_data);
        }
        }
        
    
    }
          
     
        session()->flash('success', __('site.added_successfully'));
        return redirect()->route('dashboard.Cards.index');

    }//end of store


    public function import(Request $request) 
    {

        Excel::import(new CardImport,request()->file('file'));
           
        return redirect()->back();

       $data = Excel::toArray(new CardImport, request()->file('file')); 

         collect(head($data))
            ->each(function ($row, $key) {
                $row['card_code'];
            });
            return redirect()->back();
    }


    public function edit($id )
    {
        $Companies = Company::all();
        $category=Cards::where('id',$id)->first();
      /*  if($category->offer ==1 ){
            $checked="checked";}else{
                $checked=""; 
            }*/
        return view('dashboard.Cards.edit', compact('category','Companies'));

    }//end of edit

    public function update(Request $request,$id)
    {
        $category=Cards::where('id',$id)->first();


        $request_data = $request->except(['_token', '_method']);
        
      /*  if($request->offer=="on"){
            $request_data['offer']=1;
        }else{
            $request_data['offer']=0;
        }*/
        if ($request->card_image) {

            if ($category->card_image != '') {

                Storage::disk('public_uploads')->delete('/cards/' . $category->card_image);

            }//end of if

            Image::make($request->card_image)
                ->resize(300, null, function ($constraint) {
                    $constraint->aspectRatio();
                })
                ->save(public_path('uploads/cards/' . $request->card_image->hashName()));

            $request_data['card_image'] = 
            'https://bn-plus.ly/BNplus/public/uploads/cards/'.$request->card_image->hashName();

        }//end of if



        Cards::where('id',$id)->update($request_data);
        session()->flash('success', __('site.updated_successfully'));
        return redirect()->route('dashboard.Cards.index');

    }//end of update

    public function destroy($id)
    {
        $category=Cards::where('id',$id)->first();
        dd($category);
        if ($category->card_image != '') {

            Storage::disk('public_uploads')->delete('/cards/' . $category->card_image);

        }//end of if

        Cards::where('id',$id)->delete();
        session()->flash('success', __('site.deleted_successfully'));
        return redirect()->route('dashboard.Cards.index');

    }//end of destroy

    public function offer($id )
    {
        $request_data['offer']=0;
        Cards::where('id',$id)->update($request_data);

        return redirect()->route('dashboard.Cards.index');
    }
    public function notoffer($id )
    {
        $request_data['offer']=1;
        Cards::where('id',$id)->update($request_data);

        return redirect()->route('dashboard.Cards.index');

    }


    public function cmpanies($id)
    {
        $cities = DB::table("companies")
                    ->where("kind",$id)
                    ->get();
        return json_encode($cities);
    }

}//end of controller
