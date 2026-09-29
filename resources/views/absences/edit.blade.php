@extends('layouts.app')

@section('title', 'Modifier une absence')

@section('content')
    <div class="page-header">
        <div>
            <h1>Modifier une absence</h1>
            <p class="page-intro">Mettez à jour les informations de cette absence.</p>
        </div>
    </div>
@can('absences-update')
    <form class="panel form-panel" action="{{ route('absences.update', $absence) }}" method="POST">
        @csrf
        @method('PUT')

        @include('absences._form')
    </form>
@endcan
@endsection
