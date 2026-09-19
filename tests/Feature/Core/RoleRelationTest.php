<?php

namespace Tests\Feature\Core;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class RoleRelationTest extends TestCase
{
    use RefreshDatabase;

    public function test_roles_table_has_the_expected_columns(): void
    {
        $this->assertTrue(Schema::hasColumns('roles', ['id', 'nombre']));
        $this->assertFalse(Schema::hasColumn('roles', 'created_at'));
        $this->assertTrue(Schema::hasColumn('users', 'role_id'));
    }

    public function test_user_belongs_to_a_role(): void
    {
        $role = Role::factory()->create(['nombre' => 'Administrador']);
        $user = User::factory()->for($role)->create();

        $this->assertTrue($user->role->is($role));
        $this->assertSame('Administrador', $user->fresh()->role->nombre);
    }

    public function test_role_has_many_users(): void
    {
        $role = Role::factory()->create();
        User::factory()->count(2)->for($role)->create();
        User::factory()->create();

        $this->assertCount(2, $role->users);
    }

    public function test_user_can_exist_without_a_role(): void
    {
        $user = User::factory()->create();

        $this->assertNull($user->role_id);
        $this->assertNull($user->role);
    }

    public function test_role_id_can_be_mass_assigned(): void
    {
        $role = Role::factory()->create();
        $user = User::factory()->create();

        $user->update(['role_id' => $role->id]);

        $this->assertSame($role->id, $user->fresh()->role_id);
    }
}
