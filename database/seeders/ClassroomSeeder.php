<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Classroom;

class ClassroomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('=== Importazione Aule ===');
        
        $classrooms = [
            [
                'code' => 'AULA-01',
                'name' => 'Aula 1',
                'description' => 'Aula principale per lezioni individuali',
                'capacity' => 1,
                'equipment' => ['pianoforte', 'spartiti'],
                'available' => true,
            ],
            [
                'code' => 'AULA-02',
                'name' => 'Aula 2',
                'description' => 'Aula per lezioni individuali',
                'capacity' => 1,
                'equipment' => ['pianoforte'],
                'available' => true,
            ],
            [
                'code' => 'AULA-03',
                'name' => 'Aula 3',
                'description' => 'Aula per lezioni individuali',
                'capacity' => 1,
                'equipment' => ['pianoforte'],
                'available' => true,
            ],
            [
                'code' => 'SALA-ORCH',
                'name' => 'Sala Orchestra',
                'description' => 'Sala per prove orchestra e attività collettive',
                'capacity' => 30,
                'equipment' => ['pianoforte', 'spartiti', 'sedie', 'pulpiti'],
                'available' => true,
            ],
            [
                'code' => 'SALA-CORO',
                'name' => 'Sala Coro',
                'description' => 'Sala per prove coro',
                'capacity' => 20,
                'equipment' => ['pianoforte', 'spartiti', 'sedie'],
                'available' => true,
            ],
            [
                'code' => 'LAB-01',
                'name' => 'Laboratorio 1',
                'description' => 'Laboratorio per lezioni collettive piccoli gruppi',
                'capacity' => 5,
                'equipment' => ['pianoforte', 'spartiti'],
                'available' => true,
            ],
            [
                'code' => 'LAB-02',
                'name' => 'Laboratorio 2',
                'description' => 'Laboratorio per lezioni collettive piccoli gruppi',
                'capacity' => 5,
                'equipment' => ['pianoforte', 'spartiti'],
                'available' => true,
            ],
        ];
        
        $imported = 0;
        foreach ($classrooms as $classroomData) {
            Classroom::firstOrCreate(
                ['code' => $classroomData['code']],
                $classroomData
            );
            $imported++;
        }
        
        $this->command->info("✓ Importate {$imported} aule");
    }
}
