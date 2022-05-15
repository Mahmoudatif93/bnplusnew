<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class cards_anais extends Model
{
     protected $table='cards_anais';
     
     
      protected $fillable = ['card_name','company_id','api',
     'card_price','card_code','amounts','offer','avaliable','purchase','old_price','order_id',
     'card_image','nationalcompany','productId','enable','api2','api2id'];

    public function orders()
    {
        return $this->hasMany(Order::class,'card_id','id');

    }//end of orders

    public function company()
    {
        return $this->belongsTo(Company::class);

    }//end of user
}
