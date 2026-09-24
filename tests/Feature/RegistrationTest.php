<?php

namespace Tests\Feature;

use App\Actions\Fortify\CreateNewUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
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
    }

    public function test_registration_can_create_an_admin(): void
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

        $this->assertTrue($user->isAdmin());
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'role' => 'admin',
        ]);
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
