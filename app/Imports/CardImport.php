<?php

namespace App\Imports;

use App\Cards;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
class CardImport implements ToModel, WithHeadingRow
{
    
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */

       public function model(array $row)
        {
        return new Cards([
            
            //'card_price'     => $row['card_price'],
         //   'company_id'     => $row['company_id'],
            'card_code'     => $row['card_code'],
            
        ]);
    }

 /*   public function model(array $row)
    {
     //   $Cards = new Cards();
// row[0] is the ID

      $Cards = Cards::where(array('card_price'=>$row['card_price'],'company_id'=>$row['company_id']))->first();
       // dd($Cards);
// if Cards exists and the value also exists

        if ($row['card_price']and $row['company_id']){
            $Cards->update([
                'card_code'=>$row['card_code']
            ]);
            return $Cards;
        }
    }*/


}
