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
use App\Repositories\AbsenceRepository;

class AbsenceController extends Controller
{
    private $repository;
    public function __construct(AbsenceRepository $repository)
    {
        $this->repository = $repository;
    }

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
        $this->repository->store($request->validated());
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
        $this->repository->update($absence, $request->validated());
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

        $this->repository->delete($absence);
        return redirect()
        ->route('absences.index')
        ->with('success', 'Absence supprimée avec succès')
        ;
    }

    private function joueursDisponibles(): Collection
    {
        $utilisateur = $this->utilisateurConnecte();
        $requete = Joueur::query()->orderBy('nom')->orderBy('prenom');

        if (!$utilisateur->isAdmin()) {
            $requete->where('user_id', $utilisateur->id);
        }

        return $requete->get();
    }

    private function utilisateurConnecte(): User
    {
        $utilisateur = Auth::user();

        abort_unless($utilisateur instanceof User, 401);

        return $utilisateur;
    }
}
