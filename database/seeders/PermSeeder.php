<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Perm;

class PermSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permisos = [
            [
                'username' => '',
                'name' => 'Dashboard - Inicio',
                'perm' => 'perm-1',
                'password' => '',
                'shw_password' => ''
            ],
            [
                'username' => '',
                'name' => 'Dashboard - Cuentas de usuario',
                'perm' => 'perm-1-1',
                'password' => '',
                'shw_password' => ''
            ],
            [
                'username' => '',
                'name' => 'Dashboard - Gestor de Rutas',
                'perm' => 'perm-1-2',
                'password' => '',
                'shw_password' => ''
            ],
            [
                'username' => '',
                'name' => 'Dashboard - Gestor de Cargas',
                'perm' => 'perm-1-3',
                'password' => '',
                'shw_password' => ''
            ],
            [
                'username' => '',
                'name' => 'Dashboard - Tickets',
                'perm' => 'perm-2',
                'password' => '',
                'shw_password' => ''
            ],
            [
                'username' => '',
                'name' => 'Dashboard - Dispositivos',
                'perm' => 'perm-3',
                'password' => '',
                'shw_password' => ''
            ],
        ];
        
        foreach ($permisos as $permiso) {
            Perm::create($permiso);
        }
    }
}