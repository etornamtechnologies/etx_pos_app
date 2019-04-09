<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePriceTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('price_templates', function (Blueprint $table) {
            $table->increments('id');
            $table->string('label')->unique();
            $table->text('description')->nullable();
            $table->enum('base_value', ['cost_price', 'selling_price']);
            $table->decimal('percent_value')->default(100);
            $table->decimal('constant_value')->default(0);
            $table->enum('apply_on', ['cost_price', 'selling_price']);
            $table->integer('category_id')->unsigned()->nullable();
            $table->foreign('category_id')
                  ->references('id')->on('categories')
                  ->onDelete('CASCADE')->nullable();
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
        Schema::dropIfExists('price_templates');
    }
}
