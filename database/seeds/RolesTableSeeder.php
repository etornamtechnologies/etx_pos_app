<?php

use App\Role;
use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $owner = new Role();
        $owner->label = 'manager';
        $owner->display = 'Manager';
        $owner->save();

        $sup = new Role();
        $sup->label = 'supervisor';
        $sup->display = 'Supervisor';
        $sup->save();

        $admin = new Role();
        $admin->label = 'admin';
        $admin->display = 'Admin';
        $admin->save();

        $rep = new Role();
        $rep->label = 'sales-rep';
        $rep->display = 'Sales-Rep';
        $rep->save();
    }
}
