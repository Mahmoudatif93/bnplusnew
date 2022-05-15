<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Anaiscodes extends Model
{
        protected $fillable = ['client_id','card_code','order_id'];
}
