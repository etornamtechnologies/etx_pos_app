<?php

use App\Role;
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
        $roleAdmin = Role::where('label', 'admin')->first();
        $roleSale = Role::where('label', 'sales-rep')->first();

        $admin = User::create([
            'name'=> 'etornam anyidoho',
            'username'=> 'etoretornam',
            'email'=> 'etornamanyidoho@outlook.com',
            'phone'=> '0548556086',
            'password'=> bcrypt('143541etor'),
        ]);
        $admin->roles()->attach($roleAdmin);

    }
}
