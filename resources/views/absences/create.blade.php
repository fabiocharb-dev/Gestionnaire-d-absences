@extends('layouts.app')

@section('title', 'Ajouter une absence')

@section('content')
    <div class="page-header">
        <div>
            <h1>Ajouter une absence</h1>
            <p class="page-intro">Renseignez le joueur, le motif et les dates concernées.</p>
        </div>
    </div>
@can('create', \App\Models\Absence::class)
    <form class="panel form-panel" action="{{ route('absences.store') }}" method="POST">
        @csrf

        @include('absences._form')
    </form>
@endcan
@endsection
