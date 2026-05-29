<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Crea gli utenti di staff e assegna i ruoli Spatie.
     */
    public function run(): void
    {
        // Assicura permessi e ruoli ACL (admin pieno, segreteria, teacher).
        $this->command->info('Sincronizzazione ACL (permessi + ruoli)...');
        Artisan::call('acl:sync', ['--reset-defaults' => true]);

        $staff = [
            // email, nome, ruolo, password iniziale
            ['admin@altramusica.test',        'Admin',             'admin',      'password'],
            ['barbara@laltramusica.com',      'Barbara',           'admin',      'altramusica2026'],
            ['segreteria@laltramusica.com',   'Segreteria',        'segreteria', 'altramusica2026'],
        ];

        // La colonna legacy `role` è un enum limitato; i ruoli reali sono ora gestiti da Spatie.
        $legacyEnum = ['admin', 'teacher', 'guardian', 'student'];

        foreach ($staff as [$email, $name, $role, $password]) {
            $attributes = [
                'name' => $name,
                'password' => Hash::make($password),
            ];
            if (in_array($role, $legacyEnum, true)) {
                $attributes['role'] = $role;
            }

            $user = User::firstOrCreate(['email' => $email], $attributes);

            // Assegna il ruolo Spatie (idempotente).
            if (! $user->hasRole($role)) {
                $user->assignRole($role);
            }

            $this->command->info("✓ {$role}: {$user->email}");
        }

        $this->command->warn('Password iniziali Barbara/Segreteria: "altramusica2026" — da cambiare al primo accesso.');
    }
}
