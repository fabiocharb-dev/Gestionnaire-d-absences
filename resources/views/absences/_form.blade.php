{{-- Sélection du joueur --}}
<div class="form-grid">
<div class="field">
    <label for="joueur_id">Joueur:</label>
    <select name="joueur_id" id="joueur_id" required>
        @if ($joueurs->isEmpty())
            <option value="" selected disabled>Aucun joueur associé à votre compte</option>
        @else
            <option value="">Sélectionnez un joueur</option>
            @foreach ($joueurs as $joueur)
                <option value="{{ $joueur->id }}" @selected(old('joueur_id', $absence->joueur_id ?? '') == $joueur->id)>
                    {{ $joueur->prenom }} {{ $joueur->nom }}
                </option>
            @endforeach
        @endif
    </select>
    @error('joueur_id')
    <div style="color: red" class="error">{{ $message }}</div>
@enderror
</div>

{{-- Sélection du motif --}}
<div class="field">
    <label for="motif_id">Motif:</label>
    <select name="motif_id" id="motif_id">
        <option value="">Sélectionnez un motif</option>
        @foreach ($motifs as $motif)
            <option value="{{ $motif->id }}" @selected(old('motif_id', $absence->motif_id ?? '') == $motif->id)>
                {{ $motif->description }}
            </option>
        @endforeach
    </select>
    @error('motif_id')
    <div style="color: red" class="error">{{ $message }}</div>
    @enderror
</div>

{{-- Sélection de la date de début --}}
<div class="field">
    <label for="date_debut">Date de début:</label>
    <input type="date" name="date_debut" id="date_debut" value="{{ old('date_debut', isset($absence) ? $absence->date_debut->format('Y-m-d') : '') }}">
    @error('date_debut')
    <div style="color: red" class="error">{{ $message }}</div>
    @enderror
</div>

{{-- Sélection de la date de fin --}}
<div class="field">
    <label for="date_fin">Date de fin:</label>
    <input type="date" name="date_fin" id="date_fin" value="{{ old('date_fin', isset($absence) ? $absence->date_fin->format('Y-m-d') : '') }}">
    @error('date_fin')
    <div style="color: red" class="error">{{ $message }}</div>
    @enderror
</div>
</div>

<div class="form-actions">
    <button type="submit">Enregistrer</button>
    <a class="button button-secondary" href="{{ route('absences.index') }}">Annuler</a>
</div>
