<?php

namespace Database\Seeders;

use App\Models\Specialty;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SpecialtySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $specialties = [
            'Enfermagem Geral',
            'Pediatria',
            'Geriatria',
            'Saúde Mental',
            'Obstetrícia',
            'Enfermagem do Trabalho',
            'Urgência e Emergência',
            'Enfermagem Oncológica',
            'Enfermagem Cirúrgica',
            'Enfermagem em Terapia Intensiva',
        ];

        foreach ($specialties as $name) {
            Specialty::firstOrCreate(['name' => $name]);
        }
    }
}
