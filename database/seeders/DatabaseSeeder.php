<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB; // <--- AGREGA ESTO

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call(RolesAndPermissionsSeeder::class);

        // ✅ Usuario Admin ACTIVO
        $admin = User::factory()->create([
            'name' => 'Alec Thompson',
            'email' => 'admin@corporateui.com',
            'password' => Hash::make('secret'),
            'about' => "Hi, I’m Alec Thompson...",
            // FORZAMOS EL VALOR LITERAL SQL 'true'
            'status' => DB::raw('true'), 
        ]);
        $admin->assignRole('Administrador');

        // 🔒 Usuario normal INACTIVO
        $usuario = User::factory()->create([
            'name' => 'María López',
            'email' => 'usuario@corporateui.com',
            'password' => Hash::make('1234567'),
            'about' => 'Soy una usuaria...',
            // FORZAMOS EL VALOR LITERAL SQL 'false'
            'status' => DB::raw('false'), 
        ]);
        $usuario->assignRole('Usuario');

        // 🔒 Usuario auditor INACTIVO
        $auditor = User::factory()->create([
            'name' => 'Carlos Pérez',
            'email' => 'auditor@corporateui.com',
            'password' => Hash::make('12345678'),
            'about' => 'Auditor del sistema...',
            // FORZAMOS EL VALOR LITERAL SQL 'false'
            'status' => DB::raw('false'), 
        ]);
        $auditor->assignRole('Auditor');

        $this->call(CatalogosSeeder::class);
        $this->call(BeneficioSeeder::class);
    }
}