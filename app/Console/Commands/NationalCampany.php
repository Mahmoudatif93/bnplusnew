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
     /*  
ini_set("prce.backtrack_limit","10000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000");
        
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
                $request_data=array();
                $cardsave1=array();
                foreach ($national['data'] as $companys) {

                    foreach ($companys['childs'] as $company) {
                        if (count(Company::where('id', $company['id'])->get()) == 0) {
                          
                       
                            $request_data['id'] = $company['id'];
                            $request_data['company_image'] = $company['amazonImage'];
                            $request_data['name'] = $company['categoryName'];
                            $request_data['kind'] = 'national';
                            $request_data['api'] = 1;


                            Company::create($request_data);
                            


                          //  array_push($allcompanyid, $company['id']);
                            
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
//return $allcards['data'];


                        //$cardsave = new Cards;
                        $allcardsid = array();
                        if (count($allcards) > 0) {
                            $curr =  Currency::first();
                            if (isset($allcards['data'])) {
                                foreach ($allcards['data'] as $card) {
                                    //    Cards::where('id', $card['productId'])->delete();
                                    if (count(Cards::where(array('id'=>$card['productId'],'purchase'=>0))->get()) > 0) {
                                     
                                        foreach (Cards::where('id', $card['productId'])->get() as $cardprice) {
                                            if ($cardprice->card_price != $card['sellPrice'] * $curr->amount) {
                                                $oldprice['card_price'] = $card['sellPrice'] * $curr->amount;
                                                Cards::where('id', $card['productId'])->update($oldprice);
                                            }

                                            
                                        }


                                        if (count(Company::where('id',  $card['categoryId'])->get()) > 0) {

                                            $cardsave1['productId'] =  $card['productId'];
                                            $cardsave1['company_id'] = $card['categoryId'];
                                            $cardsave1['card_name'] = $card['productName'];

                                            if ($card['productCurrency'] == "SAR") {
                                                $cardsave1['card_price']  = $card['sellPrice'] * $curr->amount;
                                            } else {
                                                $cardsave1['card_price'] = $card['sellPrice'];
                                            }
                                            $cardsave1['card_code'] = $card['productName'];
                                            $cardsave1['card_image'] = $card['productImage'];
                                            $cardsave1['nationalcompany'] = 'national';
                                            $cardsave1['api'] = 1;
                                            Cards::create($cardsave1);
                                        }
                                    } else {
                                      
                                            if (count(Company::where('id',  $card['categoryId'])->get()) > 0) {
                                                
                                                $cardsave1['productId'] =  $card['productId'];
                                                $cardsave1['company_id'] = $card['categoryId'];
                                                $cardsave1['card_name'] = $card['productName'];

                                                if ($card['productCurrency'] == "SAR") {
                                                    $cardsave1['card_price']  = $card['sellPrice'] * $curr->amount;
                                                } else {
                                                    $cardsave1['card_price'] = $card['sellPrice'];
                                                }
                                                $cardsave1['card_code'] = $card['productName'];
                                                $cardsave1['card_image'] = $card['productImage'];
                                                $cardsave1['nationalcompany'] = 'national';
                                                $cardsave1['api'] = 1;
                                                Cards::create($cardsave1);
                                            }
                                        
                                        
                                    }
                                }
                            }
                        }
                        //  return $allcardsid ;
                    }
                }
            }
        }

*/

        $this->info('National Cummand Run successfully!.');
    }
}
