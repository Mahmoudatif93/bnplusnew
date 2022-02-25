<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Company;
use App\Cards;

class CardController extends Controller
{
    use ApiResourceTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cards = Cards::where(array('avaliable' => 0,'enable'=>0))->with('company')->distinct('card_price')->distinct('card_price')->groupBy('card_price')->get();
        return $this->apiResponse($cards, 200);
    }

    public function localcards(Request $request)
    {
        if (isset($request->company_id)) {
            $cards = Cards::where(array('nationalcompany' => 'local', 'avaliable' => 0, 'purchase'=>0,'enable'=>0,'company_id' => $request->company_id))->with('company')->distinct('card_price')->groupBy('card_price')->get();
        } else {
            $cards = Cards::where(array('nationalcompany' => 'local', 'avaliable' => 0,'purchase'=>0,'enable'=>0))->with('company')->distinct('card_price')->groupBy('card_price')->get();
        }
        return $this->apiResponse($cards, 200);
    }

    public function nationalcards(Request $request)
    {



        /////////////dubi national api

        
            if (isset($request->company_id)) {
                $cards = Cards::where(array('nationalcompany' => 'national', 'avaliable' => 0,'purchase'=>0,'enable'=>0, 'company_id' => $request->company_id))->with('company')->distinct('card_price')->groupBy('card_price')->get();
                return $this->apiResponse($cards, 200);
            } else {
                $cards = Cards::where(array('nationalcompany' => 'national', 'avaliable' => 0, 'purchase'=>0,'enable'=>0))->with('company')->distinct('card_price')->groupBy('card_price')->get();
                return $this->apiResponse($cards, 200);
            }
        
    }


    public function cardsbycompany(Request $request)
    {
        if (isset($request->company_id)) {
            $cards = Cards::where(array('company_id' => $request->company_id, 'avaliable' => 0, 'purchase' => 0,'enable'=>0))->with('company')->distinct('card_price')->groupBy('card_price')->get();
        } else if (isset($request->kind)) {
            $cards = Cards::where(array('nationalcompany' => $request->kind, 'avaliable' => 0, 'purchase' => 0,'enable'=>0))->with('company')->distinct('card_price')->groupBy('card_price')->get();
        } else if (isset($request->name)) {
            $companies = Company::where('name', $request->name)->get();
            foreach ($companies as $row) {
                $cards = Cards::where(array('company_id' => $row->id, 'avaliable' => 0, 'purchase' => 0,'enable'=>0))->with('company')->distinct('card_price')->groupBy('card_price')->get();
            }
        } else {

            $cards = Cards::where(array('avaliable' => 0, 'purchase' => 0,'enable'=>0))->with('company')->distinct('card_price')->groupBy('card_price')->get();
        }



        return $this->apiResponse($cards, 200);
    }



    public function cardscount(Request $request)
    {

        $cards = Cards::where(array('id' => $request->card_id, 'avaliable' => 0, 'purchase' => 0,'enable'=>0))->count();

        if ($cards > 0) {
            $message = "Cards Avaliable ";
        } else {
            $message = "No Cards Avaliable For this Price";
        }

        return $this->apiResponse2($cards, $message, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
