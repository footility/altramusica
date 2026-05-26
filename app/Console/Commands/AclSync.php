<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class AclSync extends Command
{
    protected $signature = 'acl:sync {--migrate-users : Assegna ai utenti il ruolo Spatie dalla vecchia colonna role}';

    protected $description = 'Sincronizza permessi/ruoli ACL da config/acl.php e (opz.) migra gli utenti legacy.';

    public function handle(): int
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // 1) Genera i permessi {entita}.{azione} da config/acl.php
        $actions = array_keys(config('acl.actions'));
        $created = 0;
        foreach (config('acl.sections') as $entities) {
            foreach (array_keys($entities) as $entity) {
                foreach ($actions as $action) {
                    $perm = "{$entity}.{$action}";
                    if (Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web'])->wasRecentlyCreated) {
                        $created++;
                    }
                }
            }
        }
        $this->info("Permessi: {$created} nuovi, " . Permission::count() . " totali.");

        // 2) Ruoli di base
        foreach (config('acl.default_roles') as $roleName) {
            Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
        }

        // admin = tutti i permessi (sempre)
        Role::findByName('admin', 'web')->syncPermissions(Permission::all());

        // teacher = set minimo di sola lettura su didattica (modificabile poi via griglia)
        $teacher = Role::findByName('teacher', 'web');
        if ($teacher->permissions->isEmpty()) {
            $teacher->givePermissionTo(array_filter([
                'calendar.view', 'courses.view', 'students.view', 'exams.view',
                'teacher-availability.view', 'teacher-availability.update',
            ], fn ($p) => Permission::where('name', $p)->exists()));
        }
        $this->info('Ruoli base sincronizzati (admin pieno, teacher base).');

        // 3) Migrazione utenti legacy (opzionale, idempotente)
        if ($this->option('migrate-users')) {
            $n = 0;
            foreach (User::all() as $u) {
                $legacy = $u->getAttribute('role');
                if ($legacy && Role::where('name', $legacy)->exists() && ! $u->hasRole($legacy)) {
                    $u->assignRole($legacy);
                    $n++;
                }
            }
            $this->info("Utenti migrati al ruolo Spatie: {$n}.");
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return self::SUCCESS;
    }
}
