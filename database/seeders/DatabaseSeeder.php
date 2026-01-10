<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Ordine di esecuzione: prima entità base, poi quelle che dipendono
        $this->call([
            AdminSeeder::class,           // 1. Utente admin
            AcademicYearSeeder::class,    // 2. Anno accademico
            TeacherSeeder::class,         // 3. Docenti (necessari per corsi)
            StudentSeeder::class,         // 4. Studenti (importa anche genitori)
            GuardianSeeder::class,        // 5. Verifica relazioni genitori
            ContractSeeder::class,        // 6. Contratti (dipende da studenti)
            AvailabilitySeeder::class,     // 7. Disponibilità studenti (dipende da studenti)
            InvoiceSeeder::class,         // 8. Fatture (dipende da studenti/contratti)
            InstrumentSeeder::class,      // 9. Strumenti
            ExamSeeder::class,            // 10. Esami (dipende da studenti)
            CalendarSeeder::class,        // 11. Calendario (dipende da anno accademico)
            ClassroomSeeder::class,       // 12. Aule
            CompleteDataSeeder::class,     // 13. Importazione completa dati ODS (livelli, disponibilità, strumenti, orchestra, iscrizioni)
        ]);
    }
}
