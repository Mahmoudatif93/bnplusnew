<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Carbon\Carbon;
use App\Cards;
use App\Company;
use Illuminate\Support\Facades\Http;
use App\Currency;

class NationalCampany extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'campany:national';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        ///////////////////currancy 


        /////////////dubi national api
   /*     $allcardsid = array();
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://taxes.like4app.com/online/check_balance/",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => array(
                'deviceId' => 'cd63173e952e3076462733a26c71bbd0b236291db71656ec65ee1552478402ef',
                    'email' => 'info@bn-plus.ly',
                    'password' => 'db7d8028631f3351731cf7ca0302651d',
                    'securityCode' => 'cd63173e952e3076462733a26c71bbd077d972e07e1d416cb9ab7f87bfc0c014',
                    'langId' => '1'
            ),

        ));

        $balancenational = curl_exec($curl);

        if (isset($balancenational) && !empty($balancenational) && $balancenational != 'error code: 1020') {


            $json = json_decode($balancenational, true);
            //  return $json['balance'];


            if ($json['balance'] > 0) {

                $curl2 = curl_init();

                curl_setopt_array($curl2, array(
                    CURLOPT_URL => "https://taxes.like4app.com/online/categories",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => array(
                        'deviceId' => 'cd63173e952e3076462733a26c71bbd0b236291db71656ec65ee1552478402ef',
                        'email' => 'info@bn-plus.ly',
                        'password' => 'db7d8028631f3351731cf7ca0302651d',
                        'securityCode' => 'cd63173e952e3076462733a26c71bbd077d972e07e1d416cb9ab7f87bfc0c014',
                        'langId' => '1'
                    ),

                ));

                $companiesnational = curl_exec($curl2);

                $national = json_decode($companiesnational, true);
                //  return $national['data'];
                $compsave = new Company;
                $allcompanyid = array();
                foreach ($national['data'] as $companys) {

                    foreach ($companys['childs'] as $company) {
                        if (count(Company::where('id', $company['id'])->get()) == 0) {
                            // dd($companiesnational);
                            $compsave->id = $company['id'];
                            $compsave->company_image = $company['amazonImage'];
                            $compsave->name = $company['categoryName'];
                            $compsave->kind = 'national';
                            $compsave->api = 1;

                            $compsave->save();


                            array_push($allcompanyid, $company['id']);
                        }

                        // return($companiesnational);
                        //  return count($allcompanyid);



                        /////////////////cards 

                        $curl3 = curl_init();

                        curl_setopt_array($curl3, array(
                            CURLOPT_URL => "https://taxes.like4app.com/online/products",
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => "",
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => "POST",
                            CURLOPT_POSTFIELDS => array(
                                'deviceId' => 'cd63173e952e3076462733a26c71bbd0b236291db71656ec65ee1552478402ef',
                                'email' => 'info@bn-plus.ly',
                                'password' => 'db7d8028631f3351731cf7ca0302651d',
                                'securityCode' => 'cd63173e952e3076462733a26c71bbd077d972e07e1d416cb9ab7f87bfc0c014',
                                'langId' => '1',
                                'categoryId' => $company['id']
                                // 'ids[]' => $company['id']
                            ),

                        ));

                        $cardsnational = curl_exec($curl3);

                        $allcards = json_decode($cardsnational, true);
//return $allcards ;

                        $cardsave = new Cards;
                       
                        if (count($allcards) > 0) {
                            $curr =  Currency::first();
                            if (isset($allcards['data'])) {
                                foreach ($allcards['data'] as $card) {
                                    //    Cards::where('id', $card['productId'])->delete();


                                    
                                    if (count(Cards::where(array('productId'=>$card['productId'],'purchase'=>0))->get()) > 0) {
                                       
                                        foreach (Cards::where('productId', $card['productId'])->get() as $cardprice) {
                                            if ($cardprice->card_price != $card['sellPrice'] * $curr->amount) {
                                                $oldprice['card_price'] = $card['sellPrice'] * $curr->amount;
                                                Cards::where('id', $card['productId'])->update($oldprice);
                                            }

                                            $cardsave->productId =  $card['productId'];
                                            $cardsave->company_id = $card['categoryId'];
                                            $cardsave->card_name = $card['productName'];
                                            if ($card['productCurrency'] == "SAR") {
                                                $cardsave->card_price = $card['sellPrice'] * $curr->amount;
                                            } else {
                                                $cardsave->card_price = $card['sellPrice'];
                                            }

                                            $cardsave->card_code = $card['productName'];
                                            $cardsave->card_image = $card['productImage'];
                                            $cardsave->nationalcompany = 'national';
                                            $cardsave->api = 1;

                                        $cardsave->save();


                                        }
                                      //  array_push($allcardsid, $card['productId']);

                                        //  print_r( $oldprice);
                                    } else {
                                        if (count(Cards::where(array('productId'=>$card['productId'],'purchase'=>0))->get()) == 0) {
                                          //  array_push($allcardsid, $card['productId']);
                                            if (count(Company::where('id', $card['categoryId'])->get()) != 0) {
                                               // array_push($allcardsid, $card['productId']);
                                                $cardsave->productId =  $card['productId'];
                                                $cardsave->company_id = $card['categoryId'];
                                                $cardsave->card_name = $card['productName'];
                                                if ($card['productCurrency'] == "SAR") {
                                                    $cardsave->card_price = $card['sellPrice'] * $curr->amount;
                                                } else {
                                                    $cardsave->card_price = $card['sellPrice'];
                                                }

                                                $cardsave->card_code = $card['productName'];
                                                $cardsave->card_image = $card['productImage'];
                                                $cardsave->nationalcompany = 'national';
                                                $cardsave->api = 1;

                                            $cardsave->save();
                                            }
                                        }
                                    }
                                }
                            }
                        }
                         
                    }
                }
            }
        }
*/

        $this->info('National Cummand Run successfully!.');
    }
}
