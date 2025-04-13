<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

use App\Models\Category;
use App\Models\Status;

# Spatie set
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            StatusSeeder::class,
        ]);

        // Crear roles
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $soporte = Role::firstOrCreate(['name' => 'soporte']);
        $usuario = Role::firstOrCreate(['name' => 'usuario']);
        // $usuario = Role::create(['name' => 'usuario']);

        // Crear permisos básicos de tickets
        Permission::firstOrCreate(['name' => 'ver-tickets-propios']);
        Permission::firstOrCreate(['name' => 'ver-todos-tickets']);
        Permission::firstOrCreate(['name' => 'gestionar tickets']);
        Permission::firstOrCreate(['name' => 'crear-ticket']);
        Permission::firstOrCreate(['name' => 'editar-ticket-asignado']);
        Permission::firstOrCreate(['name' => 'editar-todos-tickets']);
        
        // Permisos de administración
        Permission::firstOrCreate(['name' => 'gestionar-usuarios']);
        Permission::firstOrCreate(['name' => 'asignar-roles']);

        // Asignar permisos a roles
        $admin->givePermissionTo([
            'ver-todos-tickets',
            'crear-ticket',
            'editar-todos-tickets',
            'gestionar-usuarios',
            'gestionar tickets',
            'asignar-roles'
        ]);

        $soporte->givePermissionTo([
            'ver-todos-tickets',
            'crear-ticket',
            'gestionar tickets',
            'editar-ticket-asignado'
        ]);

        $usuario->givePermissionTo([
            'ver-tickets-propios',
            'crear-ticket'
            
        ]);

        // Asignar rol a usuario
        $user = User::find(1);
        if ($user) {
            $user->assignRole('admin');
            $user->removeRole('usuario');
        }

        // Crear categorías
        Category::firstOrCreate(['name' => 'Software']);
        Category::firstOrCreate(['name' => 'Hardware']);
        Category::firstOrCreate(['name' => 'Redes']);

        // Crear estados
        Status::firstOrCreate(['name' => 'Abierto']);
        Status::firstOrCreate(['name' => 'En Proceso']);
        Status::firstOrCreate(['name' => 'Cerrado']);
    }
}
