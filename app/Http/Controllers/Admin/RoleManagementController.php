<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Silber\Bouncer\Database\Role;

class RoleManagementController extends Controller
{
    public function index()
    {
        $roles = Bouncer::role()
            ->with(['abilities'])
            ->withCount('users')
            ->orderBy('name')
            ->get();
        $abilities = Bouncer::ability()->orderBy('name')->get();

        return view('admin.roles.index', compact('roles', 'abilities'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'regex:/^[a-z][a-z0-9_-]*$/', Rule::unique('roles', 'name')],
            'title' => ['nullable', 'string', 'max:120'],
            'abilities' => ['sometimes', 'array'],
            'abilities.*' => ['string', 'distinct', Rule::exists('abilities', 'name')],
        ]);

        DB::transaction(function () use ($validated): void {
            $role = Bouncer::role()->create([
                'name' => $validated['name'],
                'title' => $validated['title'] ?: null,
            ]);

            Bouncer::sync($role)->abilities($validated['abilities'] ?? []);
        });

        Bouncer::refresh();

        return redirect()->route('admin.roles.index')->with('success', 'Rôle créé.');
    }

    public function updateAbilities(Request $request, Role $role)
    {
        $validated = $request->validate([
            'abilities' => ['sometimes', 'array'],
            'abilities.*' => ['string', 'distinct', Rule::exists('abilities', 'name')],
        ]);

        Bouncer::sync($role)->abilities($validated['abilities'] ?? []);
        Bouncer::refresh();

        return redirect()->route('admin.roles.index')->with('success', 'Habilitations mises à jour.');
    }

    public function storeAbility(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'regex:/^[a-z][a-z0-9_-]*$/'],
        ]);

        Bouncer::allow($role)->to($validated['name']);
        Bouncer::refresh();

        return redirect()->route('admin.roles.index')->with('success', 'Habilitation ajoutée au rôle.');
    }

    public function destroy(Role $role)
    {
        if ($role->name === 'admin') {
            return back()->withErrors(['role' => 'Le rôle admin ne peut pas être supprimé.']);
        }

        if ($role->users()->exists()) {
            return back()->withErrors(['role' => 'Réattribuez les comptes avant de supprimer ce rôle.']);
        }

        $role->delete();
        Bouncer::refresh();

        return redirect()->route('admin.roles.index')->with('success', 'Rôle supprimé.');
    }
}
