<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Beneficio; 

class BeneficioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lista de Tarifas basadas en el Reglamento Interno (Art. 22 y 24)
        $tarifas = [
            // --- INSCRIPCIONES (Art. 22.a.2) ---
            [
                'nombre'      => 'Inscripción General (Jefes de Hogar)',
                'descripcion' => 'Para casados con más de 3 años o jefes de familia.',
                'tipo'        => 'INSCRIPCION',
                'valor'       => 64.00, 
            ],
            [
                'nombre'      => 'Inscripción Especial (Solteros/Recién Casados)',
                'descripcion' => 'Tarifa reducida para solteros o uniones de hecho menores a 1 año.',
                'tipo'        => 'INSCRIPCION',
                'valor'       => 25.00, 
            ],

            // --- APORTES ANUALES (Art. 22.a.1) ---
            [
                'nombre'      => 'Aporte Anual (Estándar)',
                'descripcion' => 'Aporte anual obligatorio para miembros activos.',
                'tipo'        => 'ANUALIDAD',
                'valor'       => 10.00, 
            ],

            // --- EXONERACIONES Y SUBSIDIOS (Art. 24) ---
            [
                'nombre'      => 'Aporte Anual (Discapacidad > 50%)',
                'descripcion' => 'Subsidio del 50% presentando carnet CONADIS.',
                'tipo'        => 'ANUALIDAD',
                'valor'       => 5.00, 
            ],
            [
                'nombre'      => 'Aporte Anual (Tercera Edad 65-75 años)',
                'descripcion' => 'Subsidio del 50% por rango de edad.',
                'tipo'        => 'ANUALIDAD',
                'valor'       => 5.00, 
            ],
            [
                'nombre'      => 'Exoneración Total (Mayores de 75 años)',
                'descripcion' => 'Beneficio de gratuidad al cumplir 75 años.',
                'tipo'        => 'ANUALIDAD',
                'valor'       => 0.00, 
            ],

            // --- DERECHOS DE USO (Art. 22.b) ---
            [
                'nombre'      => 'Derecho de Uso (Nicho Plataforma)',
                'descripcion' => 'Contribución única por asignación de nicho mejorado/plataforma.',
                'tipo'        => 'DERECHO',
                'valor'       => 150.00, 
            ],
        ];

        // Iteramos y creamos uno por uno
        foreach ($tarifas as $tarifa) {
            Beneficio::create($tarifa);
        }
    }
}