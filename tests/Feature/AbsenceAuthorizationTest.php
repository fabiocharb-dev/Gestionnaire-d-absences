<?php

namespace Tests\Feature;

use App\Models\Absence;
use App\Models\Motif;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Tests\TestCase;

class AbsenceAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_sees_absences_for_all_players(): void
    {
        $user = User::factory()->has(\App\Models\Joueur::factory())->create();
        $otherUser = User::factory()->has(\App\Models\Joueur::factory())->create();
        $this->assignRoleWithAbilities($user, 'salarie', ['absences-view', 'absences-update']);
        $motif = Motif::factory()->create();

        $ownAbsence = Absence::factory()->for($user->joueur)->create([
            'motif_id' => $motif->id,
        ]);
        Absence::factory()->for($otherUser->joueur)->create([
            'motif_id' => $motif->id,
        ]);

        $response = $this->actingAs($user)->get(route('absences.index'));

        $response->assertOk();
        $response->assertSee($ownAbsence->joueur->nom);
        $response->assertSee($otherUser->joueur->nom);
    }

    public function test_salarie_with_create_ability_can_open_form_but_cannot_target_another_player(): void
    {
        $user = User::factory()->has(\App\Models\Joueur::factory())->create();
        $otherUser = User::factory()->has(\App\Models\Joueur::factory())->create();
        $this->assignRoleWithAbilities($user, 'salarie', [
            'absences-view',
            'absences-create',
            'absences-update',
        ]);
        $motif = Motif::factory()->create();

        $createResponse = $this->actingAs($user)
            ->get(route('absences.create'))
            ->assertOk();
        $createResponse->assertSee($user->joueur->prenom.' '.$user->joueur->nom);
        $createResponse->assertDontSee($otherUser->joueur->prenom.' '.$otherUser->joueur->nom);

        $response = $this->actingAs($user)->post(route('absences.store'), [
            'joueur_id' => $otherUser->joueur->id,
            'motif_id' => $motif->id,
            'date_debut' => '2026-10-01',
            'date_fin' => '2026-10-02',
        ]);

        $response->assertSessionHasErrors('joueur_id');
        $this->assertDatabaseCount('absences', 0);
    }

    public function test_user_cannot_edit_or_delete_another_players_absence(): void
    {
        $user = User::factory()->has(\App\Models\Joueur::factory())->create();
        $otherUser = User::factory()->has(\App\Models\Joueur::factory())->create();
        $this->assignRoleWithAbilities($user, 'salarie', ['absences-view', 'absences-update']);
        $motif = Motif::factory()->create();
        $ownAbsence = Absence::factory()->for($user->joueur)->create([
            'motif_id' => $motif->id,
        ]);
        $absence = Absence::factory()->for($otherUser->joueur)->create([
            'motif_id' => $motif->id,
        ]);

        $this->actingAs($user)
            ->get(route('absences.edit', $ownAbsence))
            ->assertOk();

        $this->actingAs($user)
            ->get(route('absences.edit', $absence))
            ->assertForbidden();

        $this->actingAs($user)
            ->delete(route('absences.destroy', $absence))
            ->assertForbidden();

        $this->assertDatabaseHas('absences', ['id' => $absence->id]);
    }

    public function test_admin_can_see_absences_for_all_players(): void
    {
        $admin = User::factory()->has(\App\Models\Joueur::factory())->create();
        $this->assignRoleWithAbilities($admin, 'admin', [
            'absences-view',
            'absences-create',
            'absences-update',
            'absences-delete',
        ]);
        $user = User::factory()->has(\App\Models\Joueur::factory())->create();
        $motif = Motif::factory()->create();

        Absence::factory()->for($user->joueur)->create(['motif_id' => $motif->id]);

        $response = $this->actingAs($admin)->get(route('absences.index'));

        $response->assertOk();
        $response->assertSee($user->joueur->nom);

        $createResponse = $this->actingAs($admin)
            ->get(route('absences.create'))
            ->assertOk();
        $createResponse->assertSee($admin->joueur->prenom.' '.$admin->joueur->nom);
        $createResponse->assertSee($user->joueur->prenom.' '.$user->joueur->nom);
    }

    /** @param list<string> $abilities */
    private function assignRoleWithAbilities(User $user, string $role, array $abilities): void
    {
        foreach ($abilities as $ability) {
            Bouncer::allow($role)->to($ability);
        }

        Bouncer::assign($role)->to($user);
    }
}
