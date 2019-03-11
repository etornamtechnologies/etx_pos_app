<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->string('label')->unique();
            $table->string('barcode')->unique()->nullable();
            $table->text('description', 300)->nullable();
            $table->integer('category_id')->unsigned();
            $table->foreign('category_id')
                  ->references('id')->on('categories');
            $table->integer('manufacturer_id')->unsigned()->nullable();
            $table->foreign('manufacturer_id')
                  ->references('id')->on('manufacturers');
            $table->integer('default_stock_unit')->unsigned()->nullable();
            $table->foreign('default_stock_unit')
                  ->references('id')->on('stock_units');
            $table->integer('stock_quantity')->nullable()->default(0);   
            $table->integer('reorder_quantity')->default(10); 
            $table->enum('status', ['active', 'inactive'])->default('active');
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
        Schema::dropIfExists('products');
    }
}
