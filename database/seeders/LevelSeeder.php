<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Level;

class LevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Level 1: Nivel 1: Teclas Blancas (Do a Si -> C4-B4)
        Level::create([
            'name' => 'Nivel 1: Básico (Do-Si)',
            'difficulty' => 1,
            'config' => ['C4', 'D4', 'E4', 'F4', 'G4', 'A4', 'B4'],
        ]);

        // Level 2: Nivel 2: Cromatismos (Do a Si -> C4-B4)
        Level::create([
            'name' => 'Nivel 2: Cromático (Do-Si)',
            'difficulty' => 2,
            'config' => ['C4', 'C#4', 'D4', 'D#4', 'E4', 'F4', 'F#4', 'G4', 'G#4', 'A4', 'A#4', 'B4'],
        ]);

        // Level 3: Nivel 3: Dos Octavas (C3 - B4, Chromatic)
        // We generate this procedurally to ensure correctness
        $level3Notes = [];
        $chromaticScale = ['C', 'C#', 'D', 'D#', 'E', 'F', 'F#', 'G', 'G#', 'A', 'A#', 'B'];
        for ($octave = 3; $octave <= 4; $octave++) {
            foreach ($chromaticScale as $note) {
                $level3Notes[] = $note . $octave;
            }
        }

        Level::create([
            'name' => 'Nivel 3: Dos Octavas',
            'difficulty' => 3,
            'config' => $level3Notes,
        ]);

        // Level 4: Nivel 4: Multi-Octava (C2 - C6, Chromatic)
        $level4Notes = [];
        for ($octave = 2; $octave <= 5; $octave++) {
            foreach ($chromaticScale as $note) {
                $level4Notes[] = $note . $octave;
            }
        }
        $level4Notes[] = 'C6';

        Level::create([
            'name' => 'Nivel 4: Gran Pentagrama',
            'difficulty' => 4,
            'config' => $level4Notes,
        ]);
    }
}
