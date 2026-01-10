<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creazione utente admin...');
        
        $admin = User::firstOrCreate(
            ['email' => 'admin@altramusica.test'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );
        
        $this->command->info("✓ Utente admin: {$admin->email} (password: password)");
    }
}
