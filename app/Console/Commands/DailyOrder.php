<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Cards;
use App\Order;
use App\Carbon\Carbon;
class DailyOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:daily';

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

        $allorders=Order::orderBy('id','desc')->get()->unique('card_id');

    
        if(!empty($allorders)){
            foreach($allorders as $rowa){
                ///last order\\

                $orders=Order::where(array('paid'=>'false','id'=>$rowa->id))->get();
                foreach( $orders as $row){


                    if($row->paid=="false"){
                        $is_expired = $row->created_at->addMinutes(5);
                        if($is_expired < \Carbon\Carbon::now()){
           
           
                    Cards::where(array('id'=>$row->card_id,'purchase'=>0))->update(array('avaliable'=>0));
                          
                     
                        }}
                }
              
            }
           }
           $this->info('Order Cummand Run successfully!.');

     //  $this->info('Order Cummand Run successfully!');
    }
}
