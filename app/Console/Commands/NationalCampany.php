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
                'deviceId' => '4d2ec47930a1fe0706836fdd1157a8c320dfc962aa6d0b0df2f4dda40a27b2ba',
                'email' => 'sales@bn-plus.ly',
                'password' => '149e7a5dcc2b1946ebf09f6c7684ab2c',
                'securityCode' => '4d2ec47930a1fe0706836fdd1157a8c36bd079faa0810ff7562c924a23c3f415',
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
                        'deviceId' => '4d2ec47930a1fe0706836fdd1157a8c320dfc962aa6d0b0df2f4dda40a27b2ba',
                        'email' => 'sales@bn-plus.ly',
                        'password' => '149e7a5dcc2b1946ebf09f6c7684ab2c',
                        'securityCode' => '4d2ec47930a1fe0706836fdd1157a8c36bd079faa0810ff7562c924a23c3f415',
                        'langId' => '1'
                    ),

                ));

                $companiesnational = curl_exec($curl2);

                $national = json_decode($companiesnational, true);
                //   return $national['data']['childs'];
                $compsave = new Company;
                $allcompanyid = array();
                foreach ($national['data'] as $companys) {

                    foreach ($companys['childs'] as $company) {
                        if (count(Company::where('id', $company['id'])->get()) == 0) {
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
                                'deviceId' => '4d2ec47930a1fe0706836fdd1157a8c320dfc962aa6d0b0df2f4dda40a27b2ba',
                                'email' => 'sales@bn-plus.ly',
                                'password' => '149e7a5dcc2b1946ebf09f6c7684ab2c',
                                'securityCode' => '4d2ec47930a1fe0706836fdd1157a8c36bd079faa0810ff7562c924a23c3f415',
                                'langId' => '1',
                                'categoryId' => $company['id']
                                // 'ids[]' => $company['id']
                            ),

                        ));

                        $cardsnational = curl_exec($curl3);

                        $allcards = json_decode($cardsnational, true);



                        $cardsave = new Cards;
                        // $allcardsid = array();
                        if (count($allcards) > 0) {
                            $curr =  Currency::first();
                            if (isset($allcards['data'])) {
                                foreach ($allcards['data'] as $card) {
                                    Cards::where('id', $card['productId'])->delete();

                                        if (count(Company::where('id', $card['categoryId'])->get()) != 0) {

                                            $cardsave->id =  $card['productId'];
                                            $cardsave->company_id = $card['categoryId'];
                                            $cardsave->card_name = $card['productName'];
                                            if($card['productCurrency']=="SAR"){
                                                $cardsave->card_price = $card['sellPrice'] * $curr->amount;
                                            }else{
                                                $cardsave->card_price = $card['sellPrice'] ;
                                            }
                                          
                                            $cardsave->card_code = $card['productName'];
                                            $cardsave->card_image = $card['productImage'];
                                            $cardsave->nationalcompany = 'national';
                                            $cardsave->api = 1;

                                            $cardsave->save();



                                            array_push($allcardsid, $card['productId']);
                                        } else {
                                            // return count(Company::where('id', $cards['categoryId'])->get());
                                        }
                                    
                                }
                            }
                        }
                        //  return $allcardsid ;
                    }
                }
            }
        }



        $this->info('National Cummand Run successfully!.');
    }
}
