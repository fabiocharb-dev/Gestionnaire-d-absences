@extends('layouts.app')

@section('title', 'Comptes utilisateurs')

@section('content')
    <header class="page-header">
        <div>
            <h1>Comptes utilisateurs</h1>
            <p class="page-intro">Modifiez les comptes et attribuez-leur un rôle Bouncer.</p>
        </div>
        <nav class="admin-nav" aria-label="Administration">
            <a class="button button-secondary" href="{{ route('admin.roles.index') }}">Rôles</a>
            <a class="button" href="{{ route('admin.users.index') }}" aria-current="page">Comptes</a>
            <a class="button button-secondary" href="{{ route('absences.index') }}">Absences</a>
        </nav>
    </header>

    @if (session('success'))
        <p class="alert-success">{{ session('success') }}</p>
    @endif

    @if ($errors->any())
        <div class="alert-error" role="alert">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <div class="panel table-wrapper">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Compte et modification</th>
                    <th>Joueur lié</th>
                    <th>Suppression</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr>
                        <td>
                            <form class="user-edit-form" method="POST" action="{{ route('admin.users.update', $user) }}">
                                @csrf
                                @method('PUT')
                                <div class="field">
                                    <label for="name-{{ $user->id }}">Nom</label>
                                    <input id="name-{{ $user->id }}" name="name" value="{{ $user->name }}" required>
                                </div>
                                <div class="field">
                                    <label for="email-{{ $user->id }}">Adresse e-mail</label>
                                    <input id="email-{{ $user->id }}" type="email" name="email" value="{{ $user->email }}" required>
                                </div>
                                <div class="field">
                                    <label for="role-{{ $user->id }}">Rôle</label>
                                    <select id="role-{{ $user->id }}" name="role_id" required>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->id }}" @selected($user->roles->first()?->id === $role->id)>
                                                {{ $role->title ?: $role->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <details class="password-edit">
                                    <summary>Changer le mot de passe</summary>
                                    <div class="field">
                                        <label for="password-{{ $user->id }}">Nouveau mot de passe</label>
                                        <input id="password-{{ $user->id }}" type="password" name="password" autocomplete="new-password">
                                    </div>
                                    <div class="field">
                                        <label for="password-confirmation-{{ $user->id }}">Confirmer le mot de passe</label>
                                        <input id="password-confirmation-{{ $user->id }}" type="password" name="password_confirmation" autocomplete="new-password">
                                    </div>
                                </details>
                                <button type="submit">Enregistrer</button>
                            </form>
                        </td>
                        <td>
                            @if ($user->joueur)
                                {{ $user->joueur->prenom }} {{ $user->joueur->nom }}
                            @else
                                <span class="page-intro">Aucun joueur lié</span>
                            @endif
                        </td>
                        <td>
                            @if (auth()->id() !== $user->id)
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Supprimer ce compte ? Le joueur et ses absences resteront dans la base.');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="button-danger" type="submit">Supprimer</button>
                                </form>
                            @else
                                <span class="page-intro">Compte connecté</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3">Aucun compte utilisateur.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $users->links() }}
@endsection
