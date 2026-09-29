@extends('layouts.app')

@php
    use Illuminate\Support\Facades\Auth;
@endphp

@section('title', 'Liste des absences')

@section('content')
    <div class="page-header">
        <div>
            <h1>Tableau des absences</h1>
            <p class="page-intro">Consultez et gérez les absences enregistrées.</p>
            <p class="current-user">
                Connecté en tant que {{ Auth::user()->name }}
                ({{ Auth::user()->email }} / {{ Auth::user()->getRoles()->join(', ') }})
            </p>
        </div>


        @can('create', \App\Models\Absence::class)
        <a class="button" href="{{ route('absences.create') }}">
            Ajouter une absence
        </a>
        @endcan

        @if (Auth::check() && Auth::user()->isAdmin())
            <a class="button button-secondary" href="{{ route('admin.roles.index') }}">Administration</a>
        @endif

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button class="button button-secondary" type="submit">
                Se déconnecter
            </button>
        </form>
    </div>

    @if (session('success'))
        <p class="alert-success">{{ session('success') }}</p>
    @endif

    <div class="panel table-wrapper">
        <table class="absences-table">
            <thead>
                <tr>
                    <th>Joueur</th>
                    <th>Motif</th>
                    <th>Date de début</th>
                    <th>Date de fin</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($liste as $absence)
                    <tr>
                        <td>
                            {{ $absence->joueur?->prenom }}
                            {{ $absence->joueur?->nom }}
                        </td>
                        <td>
                            {{ $absence->motif?->description }}
                        </td>
                        <td>
                            {{ $absence->date_debut?->format('d/m/Y') }}
                        </td>
                        <td>
                            {{ $absence->date_fin?->format('d/m/Y') }}
                        </td>
                        <td class="actions">
                            @can('update', $absence)
                            <a href="{{ route('absences.edit', $absence) }}">
                                Modifier
                            </a>
                            @endcan

                            @can('delete', $absence)
                            <form
                                action="{{ route('absences.destroy', $absence) }}"
                                method="POST"
                                style="display: inline;"
                                onsubmit="return confirm('Voulez-vous vraiment supprimer cette absence ?');"
                            >
                                @csrf
                                @method('DELETE')

                                <button class="button-danger" type="submit">
                                    Supprimer
                                </button>
                            </form>
                            @endcan
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection

