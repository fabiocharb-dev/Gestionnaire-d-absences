<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Silber\Bouncer\BouncerFacade as Bouncer;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     *
     * @throws ValidationException
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'nom' => ['required', 'string', 'max:25'],
            'prenom' => ['required', 'string', 'max:25'],
            'genre' => ['required', Rule::in(['homme', 'femme', 'nonbinaire'])],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class),
            ],
            'password' => $this->passwordRules(),
        ])->validate();

        return DB::transaction(function () use ($input): User {
            $user = User::create([
                'name' => $input['prenom'].' '.$input['nom'],
                'email' => $input['email'],
                'password' => Hash::make($input['password']),
            ]);

            $user->joueur()->create([
                'nom' => $input['nom'],
                'prenom' => $input['prenom'],
                'genre' => $input['genre'],
            ]);

            $role = Bouncer::role()->firstOrCreate(['name' => 'salarie']);

            if ($role->wasRecentlyCreated) {
                foreach (['absences-view', 'absences-create', 'absences-update'] as $ability) {
                    Bouncer::allow($role)->to($ability);
                }
            }

            Bouncer::assign($role)->to($user);

            return $user;
        });
    }
}
