<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = User::create([
            'role' => 1,
            'logo' =>'user-1.png',
            'logo_top' =>'logo-top.png',
            'logo_top_sm' =>'logo-sm.png',
            'name' => 'Fletes RGC',
            'email' => 'admin@admin.com',
            'password' => bcrypt('Project2023_'),
            'username' => 'Admin',
            'status' => 0,
            'shw_password' => 'Project2023_',
            'email_verified_at' => '2023-10-16 17:54:25']);
    }
}