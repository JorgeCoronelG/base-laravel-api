<?php

namespace Tests\Feature\Core;

use App\Http\Middleware\Permission;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class PermissionMiddlewareTest extends TestCase
{
    private function actAsUserWithRole(int $roleId): void
    {
        $user = new class extends User {
            public $role;
        };
        $user->role = (object) ['id' => $roleId];

        $this->actingAs($user);
    }

    private function handle(string|int ...$roleIds): Response
    {
        return (new Permission())->handle(
            Request::create('/'),
            fn () => new Response('ok'),
            ...$roleIds
        );
    }

    public function test_allows_when_role_id_matches_and_types_are_equal(): void
    {
        $this->actAsUserWithRole(1);

        $this->assertSame('ok', $this->handle(1)->getContent());
    }

    public function test_denies_when_role_does_not_match(): void
    {
        $this->actAsUserWithRole(2);

        $this->expectException(AuthorizationException::class);
        $this->handle(1);
    }

    public function test_bug_denies_matching_role_when_parameter_is_string(): void
    {
        // BUG: los parámetros de middleware ("permission:1") llegan como string y se compara con ===.
        $this->actAsUserWithRole(1);

        $this->expectException(AuthorizationException::class);
        $this->handle('1');
    }
}
