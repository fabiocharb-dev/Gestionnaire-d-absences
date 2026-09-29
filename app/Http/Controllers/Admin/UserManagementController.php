<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Silber\Bouncer\Database\Role;

class UserManagementController extends Controller
{
    public function index()
    {
        $users = User::query()
            ->with(['joueur', 'roles'])
            ->orderBy('name')
            ->paginate(20);
        $roles = Bouncer::role()->orderBy('name')->get();

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'role_id' => ['required', 'integer', Rule::exists('roles', 'id')],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $role = Bouncer::role()->findOrFail($validated['role_id']);
        $this->protectLastAdmin($user, $role);

        $user->name = $validated['name'];
        $user->email = $validated['email'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();
        Bouncer::sync($user)->roles([$role]);
        Bouncer::refresh();

        return redirect()->route('admin.users.index')->with('success', 'Compte mis à jour.');
    }

    public function destroy(Request $request, User $user)
    {
        if ($request->user()->is($user)) {
            throw ValidationException::withMessages([
                'account' => 'Vous ne pouvez pas supprimer votre propre compte depuis cette page.',
            ]);
        }

        if ($user->isAdmin() && !$this->hasAnotherAdmin($user)) {
            throw ValidationException::withMessages([
                'account' => 'Le dernier compte admin ne peut pas être supprimé.',
            ]);
        }

        $user->delete();
        Bouncer::refresh();

        return redirect()->route('admin.users.index')->with('success', 'Compte supprimé.');
    }

    private function protectLastAdmin(User $user, Role $newRole): void
    {
        if ($user->isAdmin() && $newRole->name !== 'admin' && !$this->hasAnotherAdmin($user)) {
            throw ValidationException::withMessages([
                'role_id' => 'Le dernier compte admin ne peut pas perdre son rôle.',
            ]);
        }
    }

    private function hasAnotherAdmin(User $user): bool
    {
        return User::query()
            ->where($user->getKeyName(), '!=', $user->getKey())
            ->whereHas('roles', fn ($query) => $query->where('name', 'admin'))
            ->exists();
    }
}
