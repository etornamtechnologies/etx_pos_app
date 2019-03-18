<?php

use App\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name'=> 'etornam anyidoho',
            'username'=> 'etoretornam',
            'email'=> 'etornamanyidoho@outlook.com',
            'phone'=> '0548556086',
            'password'=> bcrypt('143541etor'),
        ]);
    }
}
