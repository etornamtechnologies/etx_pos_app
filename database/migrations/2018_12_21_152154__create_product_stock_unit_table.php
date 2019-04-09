<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductStockUnitTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_stock_unit', function (Blueprint $table) {
            $table->primary(['product_id', 'stock_unit_id']);
            $table->integer('product_id')->unsigned();
            $table->foreign('product_id')
                  ->references('id')->on('products')
                  ->onDelete('cascade');

            $table->integer('stock_unit_id')->unsigned();
            $table->foreign('stock_unit_id')
                  ->references('id')->on('stock_units')
                  ->onDelete('cascade');      
            $table->integer('metric_scale')->required();
            $table->integer('selling_price')->default(0);
            $table->integer('cost_price')->default(0);
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
        Schema::dropIfExists('product_stock_unit');
    }
}
