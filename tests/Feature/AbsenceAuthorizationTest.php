<?php

namespace Tests\Feature;

use App\Models\Absence;
use App\Models\Motif;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AbsenceAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_sees_absences_for_all_players(): void
    {
        $user = User::factory()->has(\App\Models\Joueur::factory())->create();
        $otherUser = User::factory()->has(\App\Models\Joueur::factory())->create();
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

    public function test_user_cannot_create_an_absence_for_another_player(): void
    {
        $user = User::factory()->has(\App\Models\Joueur::factory())->create();
        $otherUser = User::factory()->has(\App\Models\Joueur::factory())->create();
        $motif = Motif::factory()->create();

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
        $motif = Motif::factory()->create();
        $absence = Absence::factory()->for($otherUser->joueur)->create([
            'motif_id' => $motif->id,
        ]);

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
        $admin = User::factory()->has(\App\Models\Joueur::factory())->create(['role' => 'admin']);
        $user = User::factory()->has(\App\Models\Joueur::factory())->create();
        $motif = Motif::factory()->create();

        Absence::factory()->for($user->joueur)->create(['motif_id' => $motif->id]);

        $response = $this->actingAs($admin)->get(route('absences.index'));

        $response->assertOk();
        $response->assertSee($user->joueur->nom);
    }
}
