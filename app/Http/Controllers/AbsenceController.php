<?php

namespace App\Http\Controllers;

use App\Models\Absence;
use App\Models\User;
//use Illuminate\Http\Request;
use App\Models\Motif;
use App\Models\Joueur;
use App\Http\Requests\StoreAbsenceRequest;
use App\Http\Requests\UpdateAbsenceRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;

class AbsenceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Absence::class);

        $liste = Absence::with(['motif', 'joueur'])->get();

        return view('absences.index', compact('liste'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Absence::class);

        $motifs = Motif::all();
        $joueurs = $this->joueursDisponibles();

        return view('absences.create', compact('motifs', 'joueurs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAbsenceRequest $request)
    {
        Absence::create($request->validated());
        return redirect()
        ->route('absences.index')
        ->with('success', 'Absence créée avec succès')
        ;
    }

    /**
     * Display the specified resource.
     */
    public function show(Absence $absence)
    {
        $this->authorize('view', $absence);

        return response()->json(
            $absence->load(['motif', 'joueur'])
            );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Absence $absence)
    {
        $this->authorize('update', $absence);

        $motifs = Motif::all();
        $joueurs = $this->joueursDisponibles();

        return view('absences.edit', compact('absence', 'motifs', 'joueurs'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAbsenceRequest $request, Absence $absence)
    {
        $absence->update($request->validated());
        return redirect()
        ->route('absences.index')
        ->with('success', 'Absence mise à jour avec succès')
        ;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Absence $absence)
    {
        $this->authorize('delete', $absence);

        $absence->delete();
        return redirect()
        ->route('absences.index')
        ->with('success', 'Absence supprimée avec succès')
        ;
    }

    private function joueursDisponibles(): Collection
    {
        $utilisateur = $this->utilisateurConnecte();

        if ($utilisateur->isAdmin()) {
            return Joueur::query()->orderBy('nom')->orderBy('prenom')->get();
        }

        return $utilisateur->joueur ? collect([$utilisateur->joueur]) : collect();
    }

    private function utilisateurConnecte(): User
    {
        $utilisateur = Auth::user();

        abort_unless($utilisateur instanceof User, 401);

        return $utilisateur;
    }
}
