<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('configs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('shop_name')->nullable();
            $table->string('shop_phone')->nullbale();
            $table->string('shop_address')->nullable();
            $table->string('shop_email')->nullable();
            $table->string('shop_message')->nullable();
            $table->decimal('profit_margin')->nullable();
            $table->string('sale_receipt_prefix')->default('SALINV#');
            $table->string('purchase_receipt_prefix')->default('PURINV#');
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
        Schema::dropIfExists('configs');
    }
}
