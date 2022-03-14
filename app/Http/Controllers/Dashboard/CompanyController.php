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


       
      $response = Http::withHeaders([
        'Accept' => 'application/json',
        'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiYjk3NTMxYzJkNDkzMjdhMzAwNmRjN2NiOTc4NTRlODFjMWMwYzVkYWMzN2UyNzhhZjViMjYyNmFmMTE5YjVjMDMxZTQzNGU0NDE3ODFlYjkiLCJpYXQiOjE2NDQzNTU0NjEsIm5iZiI6MTY0NDM1NTQ2MSwiZXhwIjoxNzcwNTg1ODYxLCJzdWIiOiI3Iiwic2NvcGVzIjpbXX0.s0Yat6614IuR3jMJ0njo4-50DzfSjCd5tASebIDUyUP_O_wxFp4ed3av1Dari_xDv4OBn23wjoIURUOSkuVGSz84sLTbkWrv418CzZ-ygxXHeQoyZ-JUXGbk8-1A35SEJbBQdjPI8svlVs2UL_RTlQarZbDLDMtXH5heCsf3sf0nuK79zY_bhFFAZD882P3uViYnD-YcecRGFxjmxVz3vrShspwskg-kwM1sIrmLD95lRg7n7ZJItGCyaXDC27XJuVZUhmtCLA48iFBSBoTdk1NE_5pGiWn0UOzwvdbxWfKioQoeBrdP-wVJF9MDklahycPI4wN1ooKiSeeFL3xtBHSwjpk8GP1_y3UZZl99ANlR7j_jgKj8g_VH-w3m6I8dSTkbSvclBXY8joowgguOWkn4R3QV1hQtH4w-nf_14wV90hJE1O1NNEyQ3smidSSdQp0Qd_vlTqYOTgJPzlvkERxW-T2efJ9uM_TJFRPnXbSiLugC0AIIJw9GkBDAtUEhFKazYpRX4r45bOaOUKQtO65FFf_h40MBp-0DiTL6VIZX0X-jSxeAZ75ilBQVl7TUF_-zx5YsIN2xRLqgC97aqIe80rViUFARqWAQNQFCQFfe8Z7igpb0t4L49ZJ4JykktG03k53HZN4W2GZPOT2RdI2fgQVcytXza1VfXYmU2xo',
        'X-API-KEY' => '984adf4c-44e1-418f-829b'
    ])->post('https://api.plutus.ly/api/v1/transaction/sadadapi/confirm', [

        'grant_type' => 'info@bn-plus.ly',
        'client_id'=>'bn-plus',
        'client_secret'=>'3U8F3U9C9IM39VJ39FUCLWLC872MMXOW8K2STWI28ZJD3ERF',
        'password' => 'P@ssw0rd1988',
        'email' => 'info@bn-plus.ly',

        


    ]);











        ini_set("prce.backtrack_limit","100000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000");
  

        $Companies = Company::where('enable',0)->when($request->search, function ($q) use ($request) {

            return $q->where('name','like', '%' .  $request->search . '%')
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
