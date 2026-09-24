<?php

namespace App\Services;

use App\Models\Absence;
use App\Models\Joueur;
use App\Models\Motif;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class CongePayeService
{
    private const LIMITE_JOURS_OUVRES = 25;
    private const MOTIFS_COMPTES_DANS_LE_PLAFOND = [
        'Congé Payé',
        'Congé Paternité',
        'Congé Maternité',
    ];

    public function messageErreur(array $donnees, ?Absence $absenceActuelle = null): ?string
    {
        $dateDebut = Carbon::parse($donnees['date_debut'])->startOfDay();
        $dateFin = Carbon::parse($donnees['date_fin'])->startOfDay();

        $chevauchement = Absence::query()
            ->where('joueur_id', $donnees['joueur_id'])
            ->whereDate('date_debut', '<=', $dateFin->toDateString())
            ->whereDate('date_fin', '>=', $dateDebut->toDateString())
            ->when(
                $absenceActuelle,
                fn ($requete) => $requete->where('id', '!=', $absenceActuelle->id)
            )
            ->exists();

        if ($chevauchement) {
            return 'Cette période chevauche une absence déjà enregistrée.';
        }

        $motif = Motif::find($donnees['motif_id']);
        $joueur = Joueur::find($donnees['joueur_id']);

        if ($motif?->description === 'Congé Paternité' && $joueur?->genre !== 'homme') {
            return 'Le congé paternité est réservé aux joueurs de genre homme.';
        }

        if ($motif?->description === 'Congé Maternité' && $joueur?->genre !== 'femme') {
            return 'Le congé maternité est réservé aux joueuses de genre femme.';
        }

        $motifsComptes = Motif::query()
            ->whereIn('description', self::MOTIFS_COMPTES_DANS_LE_PLAFOND)
            ->get();

        if (!$motifsComptes->contains('id', (int) $donnees['motif_id'])) {
            return null;
        }

        $motifIds = $motifsComptes->pluck('id');

        if (!$joueur) {
            $absencesExistantes = collect();
        } else {
            $absencesExistantes = Absence::query()
                ->where('joueur_id', $joueur->id)
                ->whereIn('motif_id', $motifIds)
                ->when(
                    $absenceActuelle,
                    function ($requete) use ($absenceActuelle) {
                        $requete->where('id', '!=', $absenceActuelle->id);
                    }
                )
                ->get();
        }

        for ($annee = $dateDebut->year; $annee <= $dateFin->year; $annee++) {
            $debutAnnee = Carbon::create($annee, 1, 1)->startOfDay();
            $finAnnee = Carbon::create($annee, 12, 31)->startOfDay();
            $debutPeriodeAnnee = $dateDebut->greaterThan($debutAnnee)
                ? $dateDebut->copy()
                : $debutAnnee;
            $finPeriodeAnnee = $dateFin->lessThan($finAnnee)
                ? $dateFin->copy()
                : $finAnnee;

            $joursDemandes = $this->compterJoursOuvres(
                $debutPeriodeAnnee,
                $finPeriodeAnnee
            );

            $joursDejaPris = 0;

            foreach ($absencesExistantes as $absence) {
                $absenceDebut = Carbon::parse($absence->date_debut)->startOfDay();
                $absenceFin = Carbon::parse($absence->date_fin)->startOfDay();

                if (
                    $absenceFin->lessThan($debutAnnee) ||
                    $absenceDebut->greaterThan($finAnnee)
                ) {
                    continue;
                }


                $debutAbsenceAnnee = $absenceDebut->greaterThan($debutAnnee)
                    ? $absenceDebut->copy()
                    : $debutAnnee;

                $finAbsenceAnnee = $absenceFin->lessThan($finAnnee)
                    ? $absenceFin->copy()
                    : $finAnnee;

                $joursDejaPris += $this->compterJoursOuvres(
                    $debutAbsenceAnnee,
                    $finAbsenceAnnee
                );
            }

            $totalJours = $joursDejaPris + $joursDemandes;

            if ($totalJours > self::LIMITE_JOURS_OUVRES) {
                return sprintf(
                    'La limite de %d jours ouvrés de congé payé est dépassée pour l’année %d. Total demandé : %d jours.',
                    self::LIMITE_JOURS_OUVRES,
                    $annee,
                    $totalJours
                );
            }
        }

        return null;
    }

    private function compterJoursOuvres(
        Carbon $dateDebut,
        Carbon $dateFin
    ): int {
        $nombreJours = 0;

        foreach (CarbonPeriod::create($dateDebut, $dateFin) as $date) {
            if ($date->isWeekday()) {
                $nombreJours++;
            }
        }
        return $nombreJours;
    }
}
