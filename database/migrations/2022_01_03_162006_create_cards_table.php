<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cards', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id')->unsigned();
            
            $table->string('card_name')->default(0);
            $table->double('card_price')->default(0);
            $table->string('card_code')->default(0);

            $table->integer('avaliable')->default(0);
            $table->string('nationalcompany')->default('local');
            
            $table->string('card_image')->default('default.png');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cards');
    }
}
