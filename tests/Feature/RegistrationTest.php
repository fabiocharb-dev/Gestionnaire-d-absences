<?php

namespace Tests\Feature;

use App\Actions\Fortify\CreateNewUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_creates_a_user_and_its_joueur(): void
    {
        $user = (new CreateNewUser)->create([
            'nom' => 'Dupont',
            'prenom' => 'Alice',
            'genre' => 'femme',
            'email' => 'alice@example.test',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $this->assertInstanceOf(User::class, $user);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Alice Dupont',
            'email' => 'alice@example.test',
            'role' => 'utilisateur',
        ]);
        $this->assertDatabaseHas('joueurs', [
            'user_id' => $user->id,
            'nom' => 'Dupont',
            'prenom' => 'Alice',
            'genre' => 'femme',
        ]);
        $this->assertSame($user->id, $user->joueur->user_id);
        $this->assertTrue($user->isA('salarie'));
        $this->assertTrue($user->can('absences-view'));
        $this->assertTrue($user->can('absences-create'));
        $this->assertTrue($user->can('absences-update'));
        $this->assertFalse($user->can('absences-delete'));
        $this->assertFalse($user->isAdmin());
    }

    public function test_registration_cannot_assign_the_bouncer_admin_role(): void
    {
        $user = (new CreateNewUser)->create([
            'nom' => 'Admin',
            'prenom' => 'Alice',
            'genre' => 'femme',
            'role' => 'admin',
            'email' => 'admin@example.test',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $this->assertFalse($user->isAdmin());
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'role' => 'utilisateur',
        ]);
    }

    public function test_registration_does_not_overwrite_existing_salarie_abilities(): void
    {
        Bouncer::allow('salarie')->to('absences-view');

        $user = (new CreateNewUser)->create([
            'nom' => 'Dupont',
            'prenom' => 'Alice',
            'genre' => 'femme',
            'email' => 'alice@example.test',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $this->assertTrue($user->can('absences-view'));
        $this->assertFalse($user->can('absences-create'));
        $this->assertFalse($user->can('absences-update'));
    }

    public function test_registration_requires_joueur_information_and_password_confirmation(): void
    {
        $this->expectException(ValidationException::class);

        (new CreateNewUser)->create([
            'email' => 'alice@example.test',
            'password' => 'Password123!',
            'password_confirmation' => 'different-password',
        ]);

        $this->assertDatabaseCount('users', 0);
        $this->assertDatabaseCount('joueurs', 0);
    }
}
