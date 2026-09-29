<?php

namespace Tests\Feature;

use App\Models\Joueur;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Silber\Bouncer\Database\Role;
use Tests\TestCase;

class AdminManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_pages_are_restricted_to_admin_role(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('admin.roles.index'))->assertForbidden();
        $this->actingAs($user)->get(route('admin.users.index'))->assertForbidden();

        $admin = $this->createAdmin();

        $this->actingAs($admin)->get(route('admin.roles.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.users.index'))->assertOk();
    }

    public function test_admin_can_create_role_and_manage_its_abilities(): void
    {
        $admin = $this->createAdmin();

        $this->actingAs($admin)
            ->post(route('admin.roles.store'), [
                'name' => 'observateur',
                'title' => 'Observateur',
            ])
            ->assertRedirect(route('admin.roles.index'));

        $role = Role::where('name', 'observateur')->firstOrFail();

        $this->actingAs($admin)
            ->post(route('admin.roles.abilities.store', $role), ['name' => 'absences-view'])
            ->assertRedirect(route('admin.roles.index'));

        $this->actingAs($admin)
            ->put(route('admin.roles.abilities.update', $role), [
                'abilities' => ['absences-view'],
            ])
            ->assertRedirect(route('admin.roles.index'));

        $this->assertDatabaseHas('abilities', ['name' => 'absences-view']);
        $observer = User::factory()->create();
        Bouncer::assign($role)->to($observer);
        Bouncer::refresh();

        $this->assertTrue($observer->can('absences-view'));
    }

    public function test_admin_can_edit_account_and_replace_its_role(): void
    {
        $admin = $this->createAdmin();
        Bouncer::allow('salarie')->to('absences-view');
        $user = User::factory()->create();
        Bouncer::assign('salarie')->to($user);

        $response = $this->actingAs($admin)->put(route('admin.users.update', $user), [
            'name' => 'Nouveau nom',
            'email' => 'nouveau@example.test',
            'role_id' => Bouncer::role()->where('name', 'admin')->value('id'),
            'password' => '',
            'password_confirmation' => '',
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Nouveau nom',
            'email' => 'nouveau@example.test',
        ]);
        $this->assertSame(['admin'], $user->fresh()->getRoles()->all());
    }

    public function test_admin_can_delete_account_but_not_self_or_last_admin(): void
    {
        $admin = $this->createAdmin();

        $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $admin))
            ->assertSessionHasErrors('account');

        $this->actingAs($admin)
            ->put(route('admin.users.update', $admin), [
                'name' => $admin->name,
                'email' => $admin->email,
                'role_id' => Bouncer::role()->where('name', 'salarie')->firstOrCreate(['name' => 'salarie'])->id,
            ])
            ->assertSessionHasErrors('role_id');

        $user = User::factory()->has(Joueur::factory())->create();
        $joueurId = $user->joueur->id;

        $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $user))
            ->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
        $this->assertDatabaseHas('joueurs', [
            'id' => $joueurId,
            'user_id' => null,
        ]);
    }

    private function createAdmin(): User
    {
        $admin = User::factory()->create();
        Bouncer::assign('admin')->to($admin);

        return $admin;
    }
}
