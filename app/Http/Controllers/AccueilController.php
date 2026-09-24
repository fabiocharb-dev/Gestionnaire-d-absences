<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AccueilController extends Controller
{
    public function __construct(private string $title = 'AccueilController')
    {
        $this->title = $title ?? "AccueilController";
    }
    public function index()
    {
        return view('welcome');
    }
    public function page (int $page)
    {
        return 'vous êtes à la page ' . $page;
    }
    public function calculer(float $a, float $b, string $operation)
    {
        if ($operation === '+') {
            $resultat = $a + $b;
            return "Le résultat de <b>l'addition</b> de $a et $b est : $resultat";
        } elseif ($operation === '-') {
            $resultat = $a - $b;
            return "Le résultat de la soustraction de $a et $b est : $resultat";
        } elseif ($operation === '*') {
            $resultat = $a * $b;
            return "Le résultat de la multiplication de $a et $b est : $resultat";

        } elseif ($operation === ':') {
            if ($b != 0) {
                $resultat = $a / $b;
                return "Le résultat de la division de $a par $b est : $resultat";
            } else {
                return "Erreur : division par zéro.";
            }
        } else {
            return "Opération non valide.";
        }
    }
}
