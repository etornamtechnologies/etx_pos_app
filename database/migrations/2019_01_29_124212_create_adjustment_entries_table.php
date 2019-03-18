<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdjustmentEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('adjustment_entries', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('adjustment_id')->unsigned();
            $table->foreign('adjustment_id')
                  ->references('id')->on('adjustments')->onDelete('CASCADE');

            $table->integer('product_id')->unsigned();
            $table->foreign('product_id')
                  ->references('id')->on('products');
            $table->string('product_label');

            $table->integer('stock_unit_id')->unsigned();
            $table->foreign('stock_unit_id')
                  ->references('id')->on('stock_units');
            $table->string('stock_unit_label');
                  
            $table->integer('old_quantity');
            $table->integer('new_quantity');
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
        Schema::dropIfExists('adjustment_entries');
    }
}
