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
use App\Currency;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class CompanyController extends Controller
{

    /* function generateHash($time){
        $email = strtolower('merchant-email@domain.com');
        $phone = '966577753100';
        $key = '******';
        return hash('sha256',$time.$email.$phone.$key);
      }*/


    public function index(Request $request)
    {

        /// $this->sendResetEmail('zayedmahdi@yahoo.com', 'SgiXggkL2L2080N8ab	', 'Your BNplus Code');
  /*  $dubiapi =  Cards::where('id',10477)->first();
   
        $id=80079;
                     
            //$client =  Client::where('id', $order->client_id)->first();
         //   rand();

         $uri = 'https://identity.anis.ly/connect/token';
         $params = array(
             'grant_type' => 'user_credentials',
             'client_id' => 'bn-plus',
             'client_secret' => '3U8F3U9C9IM39VJ39FUCLWLC872MMXOW8K2STWI28ZJD3ERF',
             'password' => 'P@ssw0rd1988',
             'email' => 'info@bn-plus.ly',
         );
         $response = Http::asForm()->withHeaders([])->post($uri, $params);   
         $token=$response->json()['access_token'];
         $token_type=$response->json()['token_type'];
         $alltoken=$response->json()['token_type'] .' '.$response->json()['access_token'];

 $orders = Http::withHeaders([
    'Accept' => 'application/json',
    'Authorization' => $alltoken,
   
])->post('https://gateway.anis.ly/api/consumers/v1/order'

, [

    'walletId' =>'E1521F1F-C592-42F3-7A1A-08D9F31F6661',
    'cardId' => $dubiapi->api2id,
    'pinNumber' => '1988',
    'orderId' => $id,
    'quantity' =>1,
    'TotalValue' =>$dubiapi->old_price

]

);
//dd($orders->json());
if(isset($orders->json()['data'])){
  
    $updatecardprssice['card_code'] = $orders->json()['data']['number'];
  //  dd($updatecardprssice['card_code']);
    Cards::where('id',  10477)->update($updatecardprssice);
   
    
    }

$compurlcheck='https://gateway.anis.ly/api/consumers/v1/categories/cards/'.$dubiapi->api2id.'';

$cardschek = Http::withHeaders([
'Accept' => 'application/json',
'Authorization' => $alltoken,

])->get( $compurlcheck);


if (!empty($cardschek->json()['data'])) {
    if($cardschek->json()['data']['inStock']==false){
        $updatecard['purchase'] = 1;
        $updatecard['avaliable'] = 1;
        Cards::where('id', 10477)->update($updatecard); 
    }

}
dd( Cards::where('id',  10477)->first());

      */  




        ini_set("prce.backtrack_limit", "100000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000");


        $Companies = Company::where('enable', 0)->when($request->search, function ($q) use ($request) {

            return $q->where('name', 'like', '%' .  $request->search . '%')
                ->orWhere('kind', 'like', '%' . $request->search . '%');
        })->latest()->paginate(5);

        return view('dashboard.Companies.index', compact('Companies'));
    } //end of index

    public function create()
    {
        return view('dashboard.Companies.create');
    } //end of create

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required',
            'kind' => 'required',
        ];

        $request->validate($rules);
        $request_data = $request->all();
        if ($request->company_image) {

            Image::make($request->company_image)
                ->resize(300, null, function ($constraint) {
                    $constraint->aspectRatio();
                })
                ->save(public_path('uploads/company/' . $request->company_image->hashName()));

            $request_data['company_image'] = 'https://bn-plus.ly/BNplus/public/uploads/company/' . $request->company_image->hashName();
        } //end of if

        Company::create($request_data);
        session()->flash('success', __('site.added_successfully'));
        return redirect()->route('dashboard.Companies.index');
    } //end of store

    public function edit($id)
    {
        $category = Company::where('id', $id)->first();
        return view('dashboard.Companies.edit', compact('category'));
    } //end of edit

    public function update(Request $request, $id)
    {
        $category = Company::where('id', $id)->first();


        $request_data = $request->except(['_token', '_method']);
        if ($request->company_image) {

            if ($category->company_image != '') {

                Storage::disk('public_uploads')->delete('/company/' . $category->company_image);
            } //end of if

            Image::make($request->company_image)
                ->resize(300, null, function ($constraint) {
                    $constraint->aspectRatio();
                })
                ->save(public_path('uploads/company/' . $request->company_image->hashName()));

            $request_data['company_image'] = 'https://bn-plus.ly/BNplus/public/uploads/company/' . $request->company_image->hashName();
        } //end of if



        Company::where('id', $id)->update($request_data);
        session()->flash('success', __('site.updated_successfully'));
        return redirect()->route('dashboard.Companies.index');
    } //end of update

    public function destroy($id)
    {
        $category = Company::where('id', $id)->first();
        if ($category->company_image != '') {

            Storage::disk('public_uploads')->delete('/company/' . $category->company_image);
        } //end of if

        Company::where('id', $id)->delete();
        session()->flash('success', __('site.deleted_successfully'));
        return redirect()->route('dashboard.Companies.index');
    } //end of destroy


    function generate_pdf()
    {
        $data = [
            'foo' => 'bar'
        ];
        $pdf = PDF2::loadView('dashboard.Companies.pdf', $data);
        return $pdf->stream('document.pdf');
    }

    public function sendResetEmail($user, $content, $subject)
    {

        $send =   Mail::send(
            'dashboard.Contacts.content',
            ['user' => $user, 'content' => $content, 'subject' => $subject],
            function ($message) use ($user, $subject) {
                $message->to($user);
                $message->subject("$subject");
            }
        );
    }
}//end of controller
