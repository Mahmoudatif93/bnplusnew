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
        $swaggercompanies = Http::withHeaders([
            'Content-Type' => 'application/x-www-form-urlencoded'
        ])->get('https://gateway-staging.anis.ly/api/consumers/v1/categories', []);
    //dd($swaggercompanies->json()['data']);

        if (!empty($swaggercompanies->json()['data'])) {

            foreach ($swaggercompanies->json()['data'] as $rowcomp) {
                if ($rowcomp['type'] == 'Local') {
                    if (!empty($rowcomp['subCategories'])) {
                        foreach ($rowcomp['subCategories'] as $rowsubcomp) {
                            if ($rowsubcomp['inStock'] == true) {
                                $itemcomp = Company::firstOrNew(array('idapi2' => $rowsubcomp['id']));

                                $itemcomp->idapi2 = $rowsubcomp['id'];
                                $itemcomp->company_image =  $rowsubcomp['logo'];
                                $itemcomp->name = $rowsubcomp['name'];
                                $itemcomp->kind = 'local';
                                $itemcomp->api2 = 1;
                                $itemcomp->save();
                            }
                        }
                    } else {
                        if ($rowcomp['inStock'] == true) {
                            $itemcomp = Company::firstOrNew(array('idapi2' => $rowcomp['id']));

                            $itemcomp->idapi2 = $rowcomp['id'];
                            $itemcomp->company_image =  $rowcomp['logo'];
                            $itemcomp->name = $rowcomp['name'];
                            $itemcomp->kind = 'local';
                            $itemcomp->api2 = 1;
                            $itemcomp->save();
                        }
                    }
                }else{



                    if (!empty($rowcomp['subCategories'])) {
                        foreach ($rowcomp['subCategories'] as $rowsubcomp) {
                            if ($rowsubcomp['inStock'] == true) {
                                $itemcomp = Company::firstOrNew(array('idapi2' => $rowsubcomp['id']));

                                $itemcomp->idapi2 = $rowsubcomp['id'];
                                $itemcomp->company_image =  $rowsubcomp['logo'];
                                $itemcomp->name = $rowsubcomp['name'];
                                $itemcomp->kind = 'local';
                                $itemcomp->api2 = 1;
                                $itemcomp->save();
                            }
                        }
                    } else {
                        if ($rowcomp['inStock'] == true) {
                            $itemcomp = Company::firstOrNew(array('idapi2' => $rowcomp['id']));

                            $itemcomp->idapi2 = $rowcomp['id'];
                            $itemcomp->company_image =  $rowcomp['logo'];
                            $itemcomp->name = $rowcomp['name'];
                            $itemcomp->kind = 'national';
                            $itemcomp->api2 = 1;
                            $itemcomp->save();
                        }
                    }



                }
            }
        }



        $uri = 'https://identity-staging.anis.ly/connect/token';
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
//dd($alltoken);
        $cards = Http::withHeaders([
            'Accept' => 'application/json',
            'Authorization' => $alltoken,
           
        ])->get('https://gateway-staging.anis.ly/api/consumers/v1/my-cards', [

        ]);

        if(!empty($cards->json()['data'])){
foreach($cards->json()['data'] as $allcardsapi ){
  
    if(is_array($allcardsapi)){
    foreach($allcardsapi as $cardsapi){
     //   dd($cardsapi);
    $dbCompanies = Company::where(array('enable'=>0,'api2'=>1,'name'=>$cardsapi['categoryName']))->first();
    print_r($cardsapi);echo"<br>";
    $itemcard = Cards::firstOrNew(array('api2id' =>  $cardsapi['id']));
  
                                    $itemcard->api2id = $cardsapi['id'];
                                    $itemcard->old_price=$cardsapi['price'];
                                    $itemcard->company_id = $dbCompanies->id;
                                    $itemcard->card_name = $cardsapi['cardName'];
                                    $itemcard->card_price =$cardsapi['price'];
                                    $itemcard->card_code = $cardsapi['number'];
                                    $itemcard->card_image = $cardsapi['logo'];
                                    $itemcard->nationalcompany=  $dbCompanies->kind;
                                    $itemcard->api2 = 1;
                                 //   dd($itemcard);
                                  $itemcard ->save();
                                    }}

}
}
      //  dd($cards->json()['data']);


        dd('i');



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
