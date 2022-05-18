<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Carbon\Carbon;
use App\Cards;
use App\Company;
use Illuminate\Support\Facades\Http;
use App\currancylocal;

class swaggercards extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'swagger:cards';

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
        ini_set("prce.backtrack_limit", "100000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000");




        $uri = 'https://identity.anis.ly/connect/token';
        $params = array(
            'grant_type' => 'user_credentials',
            'client_id' => 'bn-plus',
            'client_secret' => '3U8F3U9C9IM39VJ39FUCLWLC872MMXOW8K2STWI28ZJD3ERF',
            'password' => 'P@ssw0rd1988',
            'email' => 'info@bn-plus.ly',
        );
        $response = Http::asForm()->withHeaders([])->post($uri, $params);
        $token = $response->json()['access_token'];
        $token_type = $response->json()['token_type'];
        $alltoken = $response->json()['token_type'] . ' ' . $response->json()['access_token'];

        $orderswal = Http::withHeaders([
            'Accept' => 'application/json',
            'Authorization' => $alltoken,

        ])->get(
            'https://gateway.anis.ly/api/consumers/v1/transactions/E1521F1F-C592-42F3-7A1A-08D9F31F6661/current-balance'


        );




        if ($orderswal->json()['data'] > 0) {


            $uri = 'https://identity.anis.ly/connect/token';
            $params = array(
                'grant_type' => 'user_credentials',
                'client_id' => 'bn-plus',
                'client_secret' => '3U8F3U9C9IM39VJ39FUCLWLC872MMXOW8K2STWI28ZJD3ERF',
                'password' => 'P@ssw0rd1988',
                'email' => 'info@bn-plus.ly',
            );
            $response = Http::asForm()->withHeaders([])->post($uri, $params);
            $token = $response->json()['access_token'];
            $token_type = $response->json()['token_type'];
            $alltoken = $response->json()['token_type'] . ' ' . $response->json()['access_token'];








            $swaggercompanies = Http::withHeaders([
                'Content-Type' => 'application/x-www-form-urlencoded'
            ])->get('https://gateway.anis.ly/api/consumers/v1/categories', []);
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









                                    /////////////////////cards1
                                    $uri = 'https://identity.anis.ly/connect/token';
                                    $params = array(
                                        'grant_type' => 'user_credentials',
                                        'client_id' => 'bn-plus',
                                        'client_secret' => '3U8F3U9C9IM39VJ39FUCLWLC872MMXOW8K2STWI28ZJD3ERF',
                                        'password' => 'P@ssw0rd1988',
                                        'email' => 'info@bn-plus.ly',
                                    );
                                    $response = Http::asForm()->withHeaders([])->post($uri, $params);
                                    $token = $response->json()['access_token'];
                                    $token_type = $response->json()['token_type'];
                                    $alltoken = $response->json()['token_type'] . ' ' . $response->json()['access_token'];

                                    $compid = $rowsubcomp['id'];
                                    $compurl = 'https://gateway.anis.ly/api/consumers/v1/categories/' . $compid . '';

                                    $cards = Http::withHeaders([
                                        'Accept' => 'application/json',
                                        'Authorization' => $alltoken,

                                    ])->get($compurl);

                                    if (!empty($cards->json()['data']['cards'])) {

                                        foreach ($cards->json()['data']['cards'] as $cardsapi) {



                                            $dbCompanies = Company::where(array( 'api2' => 1, 'idapi2' => $compid))->first();
                                            //print_r($allcardsapi);echo"<br>";
                                            if (!empty($dbCompanies)) {
                                                $itemcard = Cards::firstOrNew(array('api2id' =>  $cardsapi['id']));
                                                if($itemcard->old_price !=$cardsapi['businessPrice']){
                                                $itemcard->api2id = $cardsapi['id'];
                                                $itemcard->old_price = $cardsapi['businessPrice'];
                                                $itemcard->company_id = $dbCompanies->id;
                                                $itemcard->card_name = $cardsapi['name'];
                                                $itemcard->card_price = $cardsapi['businessPrice'];
                                                $itemcard->card_code = $cardsapi['name'];
                                                $itemcard->card_image = $cardsapi['logo'];
                                                $itemcard->nationalcompany =  'local';
                                                $itemcard->api2 = 1;
                                                $itemcard->purchase=0;
                                                
                                                //   dd($itemcard);
                                                $itemcard->save();}
                                                
                                                  if($cardsapi['inStock']==true){
                                                    
                                                $itemcard->api2id = $cardsapi['id'];
                                              
                                                $itemcard->company_id = $dbCompanies->id;
                                                $itemcard->card_name = $cardsapi['name'];
                                          
                                                $itemcard->card_code = $cardsapi['name'];
                                                $itemcard->card_image = $cardsapi['logo'];
                                                $itemcard->nationalcompany =  'local';
                                                $itemcard->api2 = 1;
                                                $itemcard->purchase=0;
                                                
                                                //   dd($itemcard);
                                                $itemcard->save();}
                                                
                                                
                                                
                                                
                                            }
                                        }
                                    }

                                    /////////////////////////////












                                } else {

                                    $allcomsp = Company::where(array('idapi2' => $rowcomp['id']))->get();
                                    if (!empty($allcomsp)) {
                                        foreach ($allcomsp as $compa) {
                                            $allcardsq = Cards::where('company_id', $compa->id)->get();
                                            if (!empty($allcardsq)) {
                                                foreach ($allcardsq as $allcarss) {

                                                    if ($allcarss->api2 == 1) {
                                                        $compurlcheck = 'https://gateway.anis.ly/api/consumers/v1/categories/cards/' . $allcarss->api2id . '';

                                                        $cardschek = Http::withHeaders([
                                                            'Accept' => 'application/json',
                                                            'Authorization' => $alltoken,

                                                        ])->get($compurlcheck);

                                                        if (isset($cardschek->json()['data'])) {

                                                            if ($cardschek->json()['data']['inStock'] == false) {
                                                                $updatecard['purchase'] = 1;
                                                                $updatecard['avaliable'] = 1;
                                                                Cards::where('id', $allcarss->id)->update($updatecard);
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
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
















                                /////////////////////cards2
                                $uri = 'https://identity.anis.ly/connect/token';
                                $params = array(
                                    'grant_type' => 'user_credentials',
                                    'client_id' => 'bn-plus',
                                    'client_secret' => '3U8F3U9C9IM39VJ39FUCLWLC872MMXOW8K2STWI28ZJD3ERF',
                                    'password' => 'P@ssw0rd1988',
                                    'email' => 'info@bn-plus.ly',
                                );
                                $response = Http::asForm()->withHeaders([])->post($uri, $params);
                                $token = $response->json()['access_token'];
                                $token_type = $response->json()['token_type'];
                                $alltoken = $response->json()['token_type'] . ' ' . $response->json()['access_token'];

                                $compid = $rowcomp['id'];

                                $compurl = 'https://gateway.anis.ly/api/consumers/v1/categories/' . $compid . '';


                                $cards = Http::withHeaders([
                                    'Accept' => 'application/json',
                                    'Authorization' => $alltoken,

                                ])->get($compurl);

                                if (!empty($cards->json()['data']['cards'])) {

                                    foreach ($cards->json()['data']['cards'] as $cardsapi) {



                                        $dbCompanies = Company::where(array( 'api2' => 1, 'idapi2' => $compid))->first();
                                        //print_r($allcardsapi);echo"<br>";
                                        if (!empty($dbCompanies)) {
                                            $itemcard = Cards::firstOrNew(array('api2id' =>  $cardsapi['id']));
                                            if($itemcard->old_price !=$cardsapi['businessPrice']){
                                            $itemcard->api2id = $cardsapi['id'];
                                            $itemcard->old_price = $cardsapi['businessPrice'];
                                            $itemcard->company_id = $dbCompanies->id;
                                            $itemcard->card_name = $cardsapi['name'];
                                            $itemcard->card_price = $cardsapi['businessPrice'];
                                            $itemcard->card_code = $cardsapi['name'];
                                            $itemcard->card_image = $cardsapi['logo'];
                                            $itemcard->nationalcompany =  'local';
                                            $itemcard->api2 = 1;
                                            //   dd($itemcard);
                                            $itemcard->purchase=0;
                                            $itemcard->save();}
                                            
                                              if($cardsapi['inStock']==true){
                                                    
                                                $itemcard->api2id = $cardsapi['id'];
                                              
                                                $itemcard->company_id = $dbCompanies->id;
                                                $itemcard->card_name = $cardsapi['name'];
                                          
                                                $itemcard->card_code = $cardsapi['name'];
                                                $itemcard->card_image = $cardsapi['logo'];
                                                $itemcard->nationalcompany =  'local';
                                                $itemcard->api2 = 1;
                                                $itemcard->purchase=0;
                                                
                                                //   dd($itemcard);
                                                $itemcard->save();}
                                                
                                                
                                        }
                                    }
                                }

                                /////////////////////////////
























                            }
                            else {

                                $allcomsp = Company::where(array('idapi2' => $rowcomp['id']))->get();
                                if (!empty($allcomsp)) {
                                    foreach ($allcomsp as $compa) {
                                        $allcardsq = Cards::where('company_id', $compa->id)->get();
                                        if (!empty($allcardsq)) {
                                            foreach ($allcardsq as $allcarss) {

                                                if ($allcarss->api2 == 1) {
                                                    $compurlcheck = 'https://gateway.anis.ly/api/consumers/v1/categories/cards/' . $allcarss->api2id . '';

                                                    $cardschek = Http::withHeaders([
                                                        'Accept' => 'application/json',
                                                        'Authorization' => $alltoken,

                                                    ])->get($compurlcheck);

                                                    if (isset($cardschek->json()['data'])) {

                                                        if ($cardschek->json()['data']['inStock'] == false) {
                                                            $updatecard['purchase'] = 1;
                                                            $updatecard['avaliable'] = 1;
                                                            Cards::where('id', $allcarss->id)->update($updatecard);
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    } else {



                        if (!empty($rowcomp['subCategories'])) {
                            foreach ($rowcomp['subCategories'] as $rowsubcomp) {
                                if ($rowsubcomp['inStock'] == true) {
                                    $itemcomp = Company::firstOrNew(array('idapi2' => $rowsubcomp['id']));

                                    $itemcomp->idapi2 = $rowsubcomp['id'];
                                    $itemcomp->company_image =  $rowsubcomp['logo'];
                                    $itemcomp->name = $rowsubcomp['name'];
                                    $itemcomp->kind = 'national';
                                    $itemcomp->api2 = 1;
                                    $itemcomp->save();












                                    /////////////////////cards3
                                    $uri = 'https://identity.anis.ly/connect/token';
                                    $params = array(
                                        'grant_type' => 'user_credentials',
                                        'client_id' => 'bn-plus',
                                        'client_secret' => '3U8F3U9C9IM39VJ39FUCLWLC872MMXOW8K2STWI28ZJD3ERF',
                                        'password' => 'P@ssw0rd1988',
                                        'email' => 'info@bn-plus.ly',
                                    );
                                    $response = Http::asForm()->withHeaders([])->post($uri, $params);
                                    $token = $response->json()['access_token'];
                                    $token_type = $response->json()['token_type'];
                                    $alltoken = $response->json()['token_type'] . ' ' . $response->json()['access_token'];

                                    $compid = $rowsubcomp['id'];

                                    $compurl = 'https://gateway.anis.ly/api/consumers/v1/categories/' . $compid . '';

                                    $cards = Http::withHeaders([
                                        'Accept' => 'application/json',
                                        'Authorization' => $alltoken,

                                    ])->get($compurl);

                                    if (!empty($cards->json()['data']['cards'])) {

                                        foreach ($cards->json()['data']['cards'] as $cardsapi) {



                                            $dbCompanies = Company::where(array( 'api2' => 1, 'idapi2' => $compid))->first();
                                            //print_r($allcardsapi);echo"<br>";
                                            $curr =  currancylocal::first();
                                            if (!empty($dbCompanies)) {
                                                $itemcard = Cards::firstOrNew(array('api2id' =>  $cardsapi['id']));
                                                if($itemcard->old_price !=$cardsapi['businessPrice']){
                                                $itemcard->api2id = $cardsapi['id'];
                                                $itemcard->old_price = $cardsapi['businessPrice'];
                                                $itemcard->company_id = $dbCompanies->id;
                                                $itemcard->card_name = $cardsapi['name'];
                                                $itemcard->card_price = (($cardsapi['businessPrice']  * $curr->amount) / 100 ) + $cardsapi['businessPrice'];
                                                $itemcard->card_code = $cardsapi['name'];
                                                $itemcard->card_image = $cardsapi['logo'];
                                                $itemcard->nationalcompany =  'national';
                                                $itemcard->api2 = 1;
                                                //   dd($itemcard);
                                                $itemcard->purchase=0;
                                                $itemcard->save();}
                                                
                                                  if($cardsapi['inStock']==true){
                                                    
                                                $itemcard->api2id = $cardsapi['id'];
                                              
                                                $itemcard->company_id = $dbCompanies->id;
                                                $itemcard->card_name = $cardsapi['name'];
                                          
                                                $itemcard->card_code = $cardsapi['name'];
                                                $itemcard->card_image = $cardsapi['logo'];
                                                $itemcard->nationalcompany =  'national';
                                                $itemcard->api2 = 1;
                                                $itemcard->purchase=0;
                                                
                                                //   dd($itemcard);
                                                $itemcard->save();}
                                                
                                                
                                            }
                                        }
                                    }

                                    /////////////////////////////









                                }
                                else {

                                    $allcomsp = Company::where(array('idapi2' => $rowcomp['id']))->get();
                                    if (!empty($allcomsp)) {
                                        foreach ($allcomsp as $compa) {
                                            $allcardsq = Cards::where('company_id', $compa->id)->get();
                                            if (!empty($allcardsq)) {
                                                foreach ($allcardsq as $allcarss) {

                                                    if ($allcarss->api2 == 1) {
                                                        $compurlcheck = 'https://gateway.anis.ly/api/consumers/v1/categories/cards/' . $allcarss->api2id . '';

                                                        $cardschek = Http::withHeaders([
                                                            'Accept' => 'application/json',
                                                            'Authorization' => $alltoken,

                                                        ])->get($compurlcheck);

                                                        if (isset($cardschek->json()['data'])) {

                                                            if ($cardschek->json()['data']['inStock'] == false) {
                                                                $updatecard['purchase'] = 1;
                                                                $updatecard['avaliable'] = 1;
                                                                Cards::where('id', $allcarss->id)->update($updatecard);
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
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






                                /////////////////////cards4
                                $uri = 'https://identity.anis.ly/connect/token';
                                $params = array(
                                    'grant_type' => 'user_credentials',
                                    'client_id' => 'bn-plus',
                                    'client_secret' => '3U8F3U9C9IM39VJ39FUCLWLC872MMXOW8K2STWI28ZJD3ERF',
                                    'password' => 'P@ssw0rd1988',
                                    'email' => 'info@bn-plus.ly',
                                );
                                $response = Http::asForm()->withHeaders([])->post($uri, $params);
                                $token = $response->json()['access_token'];
                                $token_type = $response->json()['token_type'];
                                $alltoken = $response->json()['token_type'] . ' ' . $response->json()['access_token'];

                                $compid = $rowcomp['id'];

                                $compurl = 'https://gateway.anis.ly/api/consumers/v1/categories/' . $compid . '';

                                $cards = Http::withHeaders([
                                    'Accept' => 'application/json',
                                    'Authorization' => $alltoken,

                                ])->get($compurl);


                                if (!empty($cards->json()['data']['cards'])) {

                                    foreach ($cards->json()['data']['cards'] as $cardsapi) {



                                        $dbCompanies = Company::where(array( 'api2' => 1, 'idapi2' => $compid))->first();
                                        //print_r($allcardsapi);echo"<br>";
                                        $curr =  currancylocal::first();
                                        if (!empty($dbCompanies)) {
                                            $itemcard = Cards::firstOrNew(array('api2id' =>  $cardsapi['id']));
                                            if($itemcard->old_price !=$cardsapi['businessPrice']){

                                           
                                            $itemcard->api2id = $cardsapi['id'];
                                            $itemcard->old_price = $cardsapi['businessPrice'];
                                            $itemcard->company_id = $dbCompanies->id;
                                            $itemcard->card_name = $cardsapi['name'];
                                            $itemcard->card_price =  (($cardsapi['businessPrice']  * $curr->amount) / 100 ) + $cardsapi['businessPrice'];
                                            $itemcard->card_code = $cardsapi['name'];
                                            $itemcard->card_image = $cardsapi['logo'];
                                            $itemcard->nationalcompany =  'national';
                                            $itemcard->api2 = 1;
                                            //   dd($itemcard);
                                            $itemcard->purchase=0;
                                            $itemcard->save(); }
                                            
                                              if($cardsapi['inStock']==true){
                                                    
                                                $itemcard->api2id = $cardsapi['id'];
                                              
                                                $itemcard->company_id = $dbCompanies->id;
                                                $itemcard->card_name = $cardsapi['name'];
                                          
                                                $itemcard->card_code = $cardsapi['name'];
                                                $itemcard->card_image = $cardsapi['logo'];
                                                $itemcard->nationalcompany =  'national';
                                                $itemcard->api2 = 1;
                                                $itemcard->purchase=0;
                                                
                                                //   dd($itemcard);
                                                $itemcard->save();}
                                        }
                                    }
                                }

                                /////////////////////////////


















                            }
                            else {

                                $allcomsp = Company::where(array('idapi2' => $rowcomp['id']))->get();
                                if (!empty($allcomsp)) {
                                    foreach ($allcomsp as $compa) {
                                        $allcardsq = Cards::where('company_id', $compa->id)->get();
                                        if (!empty($allcardsq)) {
                                            foreach ($allcardsq as $allcarss) {

                                                if ($allcarss->api2 == 1) {
                                                    $compurlcheck = 'https://gateway.anis.ly/api/consumers/v1/categories/cards/' . $allcarss->api2id . '';

                                                    $cardschek = Http::withHeaders([
                                                        'Accept' => 'application/json',
                                                        'Authorization' => $alltoken,

                                                    ])->get($compurlcheck);

                                                    if (isset($cardschek->json()['data'])) {

                                                        if ($cardschek->json()['data']['inStock'] == false) {
                                                            $updatecard['purchase'] = 1;
                                                            $updatecard['avaliable'] = 1;
                                                            Cards::where('id', $allcarss->id)->update($updatecard);
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
                }
            }
        }



        $this->info('swagger Cummand Run successfully!.');
    }
}
