<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order_anais extends Model
{
    
    protected $table='orders';
    protected $fillable = ['card_id', 'client_id','transaction_id','card_price','paymenttype','client_name','client_number','paid','invoice_no','process_id'];



    public function client()
    {
        return $this->belongsTo(Client::class);

    }//end of cards
    
        public function cards()
    {
       return $this->belongsTo(cards_anais::class,'id','order_id');
        
        

    
    }//end of cards



}
