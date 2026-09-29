@extends('layouts.app')

@section('title', 'Rôles et habilitations')

@section('content')
    <header class="page-header">
        <div>
            <h1>Rôles et habilitations</h1>
            <p class="page-intro">Créez les rôles et choisissez les actions autorisées pour chacun.</p>
        </div>
        <nav class="admin-nav" aria-label="Administration">
            <a class="button" href="{{ route('admin.roles.index') }}" aria-current="page">Rôles</a>
            <a class="button button-secondary" href="{{ route('admin.users.index') }}">Comptes</a>
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

    <section class="admin-section">
        <h2>Créer un rôle</h2>
        <form class="panel form-panel admin-form" method="POST" action="{{ route('admin.roles.store') }}">
            @csrf
            <div class="field">
                <label for="name">Nom technique</label>
                <input id="name" name="name" required pattern="[a-z][a-z0-9_-]*" value="{{ old('name') }}" placeholder="ex. gestionnaire">
            </div>
            <div class="field">
                <label for="title">Libellé affiché</label>
                <input id="title" name="title" value="{{ old('title') }}" placeholder="ex. Gestionnaire">
            </div>

            @if ($abilities->isNotEmpty())
                <fieldset class="ability-list">
                    <legend>Habilitations initiales</legend>
                    @foreach ($abilities as $ability)
                        <label>
                            <input type="checkbox" name="abilities[]" value="{{ $ability->name }}">
                            {{ $ability->title ?: $ability->name }}
                        </label>
                    @endforeach
                </fieldset>
            @endif

            <button type="submit">Créer le rôle</button>
        </form>
    </section>

    <section class="admin-section">
        <h2>Rôles existants</h2>
        <div class="admin-role-list">
            @forelse ($roles as $role)
                <article class="panel admin-role">
                    <div class="admin-role-heading">
                        <div>
                            <h3>{{ $role->title ?: $role->name }}</h3>
                            <p class="page-intro"><code>{{ $role->name }}</code> · {{ $role->users_count }} compte(s)</p>
                        </div>
                        @if ($role->name !== 'admin' && $role->users_count === 0)
                            <form method="POST" action="{{ route('admin.roles.destroy', $role) }}" onsubmit="return confirm('Supprimer ce rôle ?');">
                                @csrf
                                @method('DELETE')
                                <button class="button-danger" type="submit">Supprimer le rôle</button>
                            </form>
                        @endif
                    </div>

                    <form method="POST" action="{{ route('admin.roles.abilities.update', $role) }}">
                        @csrf
                        @method('PUT')
                        <fieldset class="ability-list">
                            <legend>Habilitations accordées</legend>
                            @forelse ($abilities as $ability)
                                <label>
                                    <input
                                        type="checkbox"
                                        name="abilities[]"
                                        value="{{ $ability->name }}"
                                        @checked($role->abilities->contains('name', $ability->name))
                                    >
                                    {{ $ability->title ?: $ability->name }}
                                </label>
                            @empty
                                <p class="page-intro">Aucune habilitation définie.</p>
                            @endforelse
                        </fieldset>
                        <button type="submit">Enregistrer les habilitations</button>
                    </form>

                    <form class="inline-form add-ability-form" method="POST" action="{{ route('admin.roles.abilities.store', $role) }}">
                        @csrf
                        <div class="field">
                            <label for="ability-{{ $role->id }}">Ajouter une habilitation</label>
                            <input id="ability-{{ $role->id }}" name="name" required pattern="[a-z][a-z0-9_-]*" placeholder="ex. absences-export">
                        </div>
                        <button type="submit">Ajouter</button>
                    </form>
                </article>
            @empty
                <p>Aucun rôle n’a encore été créé.</p>
            @endforelse
        </div>
    </section>
@endsection
