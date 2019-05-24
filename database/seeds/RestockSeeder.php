<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RestockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('products')->update(['reorder_quantity'=> 0]);
    }
}
