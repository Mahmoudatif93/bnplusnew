<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Company;

class CompanyController extends Controller
{
    use ApiResourceTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $companies = Company::all();
        return $this->apiResponse($companies, 200);
    }

    public function allcompanies(Request $request)
    {
        if (isset($request->kind)) {
            if ($request->kind == "national") {
                /////////////dubi national api
                $balancenational = Http::withHeaders([
                    'Content-Type' => 'application/x-www-form-urlencoded'
                ])->post('https://taxes.like4app.com/online/check_balance/', [
                    'deviceId' => '111',
                    'email' => 'c',
                    'password' => 'c',
                    'securityCode' => 'c',
                    'langId' => 1,
                ]);
                if (isset($balancenational) && !empty($balancenational) && $balancenational != 'error code: 1020') {
                    // return $balancenational;
                    if ($balancenational->balance > 0) {
                        $nationalApicompany = Http::withHeaders([
                            'Content-Type' => 'application/x-www-form-urlencoded'
                        ])->post('https://taxes.like4app.com/online/categories', [
                            'deviceId' => '111',
                            'email' => 'c',
                            'password' => 'c',
                            'securityCode' => 'c',
                            'langId' => 1,
                        ]);


                        return $nationalApicompany;

                        ////////////////end//////////////
                    } else {
                        
                        $companies = Company::where(array('kind'=>'national','enable'=>0))->get();
                        return $this->apiResponse($companies, 200);
                    }
                } else {
                    $companies = Company::where(array('kind'=>'national','enable'=>0))->get();
                    return $this->apiResponse($companies, 200);
                }
            } else {
                $companies = Company::where('kind', 'local')->get();
                return $this->apiResponse($companies, 200);
            }
        } else if ($request->name) {
            $companies = Company::where(array('name'=>$request->name,'enable'=>0))->get();
            return $this->apiResponse($companies, 200);
        } else {
            $companies = Company::where('enable',0)->get();
            return $this->apiResponse($companies, 200);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $post = new posts();
        $post->title = $request->title;
        $post->body = $request->body;
        if ($post->save()) {
            // return response()->json(['status'=>'success']);
            return  $this->apiResponse('', 200);
        } else {
            //  return response()->json(['status'=>'error']);
            return $this->apiResponse('', 'erro to stor post', 404);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return posts::find($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $post = posts::find($id);
        $post->title = $request->title;
        $post->body = $request->body;
        //  dd($request->title);
        if ($post->update()) {
            return response()->json(['status' => 'success']);
        } else {
            return response()->json(['status' => 'error']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post = posts::find($id);

        if ($post->delete()) {
            return response()->json(['status' => 'success']);
        } else {
            return response()->json(['status' => 'error']);
        }
    }

    public function check_balance()
    {

        $balancenational = Http::withHeaders([
            'Content-Type' => 'application/x-www-form-urlencoded'
        ])->post('https://taxes.like4app.com/online/check_balance', [
            'deviceId' => 'cd63173e952e3076462733a26c71bbd0b236291db71656ec65ee1552478402ef',
                    'email' => 'info@bn-plus.ly',
                    'password' => 'db7d8028631f3351731cf7ca0302651d',
                    'securityCode' => 'cd63173e952e3076462733a26c71bbd077d972e07e1d416cb9ab7f87bfc0c014',
                    'langId' => '1'
        ]);

        return $balancenational;
        return $this->apiResponse($balancenational, 200);
        //return $balancenational ;
    }
}
