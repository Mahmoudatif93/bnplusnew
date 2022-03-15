<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Carbon\Carbon;
use App\Cards;
use App\Company;
use Illuminate\Support\Facades\Http;
use App\Currency;

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
        ini_set("prce.backtrack_limit","100000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000");
    

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
    //print_r($allcardsapi);echo"<br>";
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

        $this->info('swagger Cummand Run successfully!.');
    }
}
