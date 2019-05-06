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
        $roleSup = Role::where('label', 'supervisor')->first();
        $admin = User::create([
            'name'=> 'etornam anyidoho',
            'username'=> 'etoretornam',
            'email'=> 'etornamanyidoho@outlook.com',
            'phone'=> '0548556086',
            'password'=> bcrypt('143541etor'),
        ]);

        $sup = User::create([
            'name'=> 'grace amevor',
            'username'=> 'gracious',
            'email'=> 'gracius@outlook.com',
            'phone'=> '0548557383',
            'password'=> bcrypt('gracious'),
        ]);
        // $admin = User::create([
        //     'name'=> 'david admin',
        //     'username'=> 'david_admin',
        //     'email'=> 'davidadmin@outlook.com',
        //     'phone'=> '0093838737',
        //     'password'=> bcrypt('david_admin'),
        // ]);
        $admin->roles()->attach($roleAdmin);
        $sup->roles()->attach($roleSup);
    }
}
