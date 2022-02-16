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
class CompanyController extends Controller
{
    public function index(Request $request)
    {


        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://taxes.like4app.com/online/create_order",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => array(
                'deviceId' => '4d2ec47930a1fe0706836fdd1157a8c320dfc962aa6d0b0df2f4dda40a27b2ba',
                'email' => 'sales@bn-plus.ly',
                'password' => '149e7a5dcc2b1946ebf09f6c7684ab2c',
                'securityCode' => '4d2ec47930a1fe0706836fdd1157a8c36bd079faa0810ff7562c924a23c3f415',
                'langId' => '1',
                'productId' => '987',
                'referenceId' => 'Merchant_12467'
            ),

        ));

        $createorder = curl_exec($curl);
        curl_close($curl);
dd( $createorder);

      
                 
 //$this->sendResetEmail('mahmoudatif22@gmail.com', 'mm', 'test');


        $Companies = Company::when($request->search, function ($q) use ($request) {

            return $q->where('name', '%' . $request->search . '%');
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
