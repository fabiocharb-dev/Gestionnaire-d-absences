<?php

namespace App\Http\Requests;

use App\Models\Absence;
use App\Models\Joueur;
use App\Models\Motif;
use App\Services\CongePayeService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Exists;
use Illuminate\Validation\Validator;

class StoreAbsenceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('create', Absence::class) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
    * @return array<string, array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'joueur_id' => [$this->joueurRule()],
            'motif_id' => ['required', 'exists:motifs,id'],
            'date_debut' => ['required', 'date'],
            'date_fin' => ['required', 'date', 'after_or_equal:date_debut'],
        ];
        return $rules ;
    }
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $this->ajouterErreurGenre($validator);

            /*
             * On ne lance pas la règle métier si les champs de base
             * sont déjà invalides.
             */
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $message = app(CongePayeService::class)
                ->messageErreur($this->validated());

            if ($message) {
                $validator->errors()->add('date_fin', $message);
            }
        });
    }

    private function ajouterErreurGenre(Validator $validator): void
    {
        $motif = Motif::find($this->input('motif_id'));
        $genre = Joueur::find($this->input('joueur_id'))?->genre;

        if ($motif?->description === 'Congé Paternité' && $genre !== 'homme') {
            $validator->errors()->add(
                'joueur_id',
                'Le congé paternité est réservé aux joueurs de genre homme.'
            );
        }

        if ($motif?->description === 'Congé Maternité' && $genre !== 'femme') {
            $validator->errors()->add(
                'joueur_id',
                'Le congé maternité est réservé aux joueuses de genre femme.'
            );
        }
    }

    private function joueurRule(): Exists
    {
        $rule = Rule::exists('joueurs', 'id');

        if (!$this->user()?->isAdmin()) {
            $rule->where('user_id', $this->user()?->id);
        }

        return $rule;
    }
}
