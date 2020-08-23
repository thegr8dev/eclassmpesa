<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMpesatxnTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('mpesatxn')){
            Schema::create('mpesatxn', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('checkoutid');
                $table->string('rcode');
                $table->string('rdesc');
                $table->string('txnid');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mpesatxn');
    }
}
