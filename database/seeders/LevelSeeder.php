<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Level::create([
            'name' => 'Nivel 1: Teclas Blancas',
            'difficulty' => 1,
            'config' => json_encode(['C4', 'D4', 'E4', 'F4', 'G4', 'A4', 'B4']),
        ]);

        \App\Models\Level::create([
            'name' => 'Nivel 2: Teclas Negras',
            'difficulty' => 2,
            'config' => json_encode(['C#4', 'D#4', 'F#4', 'G#4', 'A#4']),
        ]);

        \App\Models\Level::create([
            'name' => 'Nivel 3: Escala de Do Mayor',
            'difficulty' => 3,
            'config' => json_encode(['C4', 'D4', 'E4', 'F4', 'G4', 'A4', 'B4', 'C5']),
        ]);
    }
}
