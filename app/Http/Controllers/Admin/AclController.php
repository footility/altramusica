<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class AclController extends Controller
{
    /** Mostra la griglia ruoli × permessi. */
    public function index(Request $request)
    {
        $roles = Role::orderBy('name')->get();
        $selected = $request->query('role', optional($roles->firstWhere('name', '!=', 'admin'))->name ?? $roles->first()?->name);
        $role = $selected ? Role::where('name', $selected)->first() : null;

        $granted = $role ? $role->permissions->pluck('name')->flip() : collect();

        return view('admin.acl.index', [
            'roles'    => $roles,
            'role'     => $role,
            'sections' => config('acl.sections'),
            'actions'  => config('acl.actions'),
            'granted'  => $granted,
            'locked'   => $role && $role->name === 'admin', // admin = sempre tutti i permessi
        ]);
    }

    /** Salva i permessi spuntati per il ruolo. */
    public function update(Request $request, Role $role)
    {
        if ($role->name === 'admin') {
            $role->syncPermissions(Permission::all()); // admin resta pieno
            return back()->with('success', 'Il ruolo "admin" ha sempre tutti i permessi.');
        }

        $perms = collect($request->input('permissions', []))
            ->filter(fn ($v, $name) => Permission::where('name', $name)->exists())
            ->keys()
            ->all();

        $role->syncPermissions($perms);
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return back()->with('success', "Permessi aggiornati per il ruolo \"{$role->name}\".");
    }

    /** Crea un nuovo ruolo configurabile. */
    public function storeRole(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:50|regex:/^[a-z0-9_\- ]+$/i|unique:roles,name',
        ]);

        $role = Role::create(['name' => trim($data['name']), 'guard_name' => 'web']);

        return redirect()->route('admin.acl.index', ['role' => $role->name])
            ->with('success', "Ruolo \"{$role->name}\" creato. Spunta i permessi.");
    }
}
