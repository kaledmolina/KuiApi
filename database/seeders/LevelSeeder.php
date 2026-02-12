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
        // Clear existing levels to avoid duplicates if re-seeding
        // Level::truncate(); // Be careful with truncate on foreign keys, usually better to updateOrCreate or delete

        // Helper for chromatic scale
        $notes = ['C', 'C#', 'D', 'D#', 'E', 'F', 'F#', 'G', 'G#', 'A', 'A#', 'B'];
        $whiteKeys = ['C', 'D', 'E', 'F', 'G', 'A', 'B'];

        // Level 1: Nivel 1 (Do-Si - White Keys Only - Octave 4)
        Level::updateOrCreate(
            ['difficulty' => 1],
            [
                'name' => 'Nivel 1: Básico (Teclas Blancas)',
                'config' => ['C4', 'D4', 'E4', 'F4', 'G4', 'A4', 'B4'],
            ]
        );

        // Level 2: Nivel 2 (Do-Si - Chromatic - Octave 4)
        $level2Notes = [];
        foreach ($notes as $note) {
            $level2Notes[] = $note . '4';
        }

        Level::updateOrCreate(
            ['difficulty' => 2],
            [
                'name' => 'Nivel 2: Cromático (Octava 4)',
                'config' => $level2Notes,
            ]
        );

        // Level 3: Nivel 3 (Two Octaves C3-B4 - White Keys Only)
        $level3Notes = [];
        for ($octave = 3; $octave <= 4; $octave++) {
            foreach ($whiteKeys as $note) {
                $level3Notes[] = $note . $octave;
            }
        }

        Level::updateOrCreate(
            ['difficulty' => 3],
            [
                'name' => 'Nivel 3: Avanzado (Dos Octavas - Blancas)',
                'config' => $level3Notes,
            ]
        );

        // Level 4: Nivel 4 (Two Octaves C3-B4 - Chromatic)
        $level4Notes = [];
        for ($octave = 3; $octave <= 4; $octave++) {
            foreach ($notes as $note) {
                $level4Notes[] = $note . $octave;
            }
        }

        Level::updateOrCreate(
            ['difficulty' => 4],
            [
                'name' => 'Nivel 4: Experto (Dos Octavas - Cromático)',
                'config' => $level4Notes,
            ]
        );

        // Level 5: Nivel 5 (Full Range C2-C6)
        $level5Notes = [];
        for ($octave = 2; $octave <= 5; $octave++) {
            foreach ($notes as $note) {
                $level5Notes[] = $note . $octave;
            }
        }
        $level5Notes[] = 'C6';

        Level::updateOrCreate(
            ['difficulty' => 5],
            [
                'name' => 'Nivel 5: Maestro (Gran Piano)',
                'config' => $level5Notes,
            ]
        );
    }
}
