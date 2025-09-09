<?php

namespace Tests\Unit;

use Tests\TestCase;
use Modules\User\Services\AuthService;
use Modules\User\Interfaces\AuthInterface;
use Modules\User\Entities\User;
use Modules\User\Interfaces\UserInterestTypes;
use Mockery;

class AuthServiceTest extends TestCase
{
    protected AuthService $authService;
    protected $mockAuthRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mockAuthRepository = Mockery::mock(AuthInterface::class);
        $this->authService = new AuthService($this->mockAuthRepository);
    }

    public function test_register_user_success(): void
    {
        $userData = [
            'full_name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'phone' => '+1234567890',
            'username' => 'johndoe',
            'country_id' => 1,
        ];

        $user = new User($userData);
        $user->id = 1;

        $this->mockAuthRepository
            ->shouldReceive('register')
            ->with($userData)
            ->once()
            ->andReturn($user);

        $result = $this->authService->register($userData);

        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals('John Doe', $result->full_name);
        $this->assertEquals('john@example.com', $result->email);
    }

    public function test_register_user_with_categories(): void
    {
        $userData = [
            'full_name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'phone' => '+1234567890',
            'username' => 'johndoe',
            'country_id' => 1,
            'categories' => [1, 2, 3],
        ];

        $user = Mockery::mock(User::class);
        $user->shouldReceive('syncInterests')
            ->with([1, 2, 3], UserInterestTypes::CATEGORY)
            ->once();

        $this->mockAuthRepository
            ->shouldReceive('register')
            ->with($userData)
            ->once()
            ->andReturn($user);

        $result = $this->authService->register($userData);

        $this->assertInstanceOf(User::class, $result);
    }

    public function test_login_success(): void
    {
        $user = new User([
            'full_name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password123'),
            'username' => 'johndoe',
            'country_id' => 1,
        ]);

        $this->mockAuthRepository
            ->shouldReceive('login')
            ->with('john@example.com', 'password123')
            ->once()
            ->andReturn($user);

        $result = $this->authService->login('john@example.com', 'password123');

        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals('john@example.com', $result->email);
    }

    public function test_login_failed(): void
    {
        $this->mockAuthRepository
            ->shouldReceive('login')
            ->with('john@example.com', 'wrongpassword')
            ->once()
            ->andReturn(null);

        $result = $this->authService->login('john@example.com', 'wrongpassword');

        $this->assertNull($result);
    }

    public function test_logout_success(): void
    {
        $user = new User(['id' => 1]);

        $this->mockAuthRepository
            ->shouldReceive('logout')
            ->with($user, 0)
            ->once()
            ->andReturn(true);

        $result = $this->authService->logout($user, 0);

        $this->assertTrue($result);
    }

    public function test_logout_failed(): void
    {
        $user = new User(['id' => 1]);

        $this->mockAuthRepository
            ->shouldReceive('logout')
            ->with($user, 0)
            ->once()
            ->andReturn(false);

        $result = $this->authService->logout($user, 0);

        $this->assertFalse($result);
    }

    public function test_is_authenticated_true(): void
    {
        $this->mockAuthRepository
            ->shouldReceive('isAuthenticated')
            ->once()
            ->andReturn(true);

        $result = $this->authService->isAuthenticated();

        $this->assertTrue($result);
    }

    public function test_is_authenticated_false(): void
    {
        $this->mockAuthRepository
            ->shouldReceive('isAuthenticated')
            ->once()
            ->andReturn(false);

        $result = $this->authService->isAuthenticated();

        $this->assertFalse($result);
    }

    public function test_get_authenticated_user(): void
    {
        $user = new User();
        $user->id = 1;

        $this->mockAuthRepository
            ->shouldReceive('getAuthenticatedUser')
            ->once()
            ->andReturn($user);

        $result = $this->authService->getAuthenticatedUser();

        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals(1, $result->id);
    }

    public function test_get_authenticated_user_null(): void
    {
        $this->mockAuthRepository
            ->shouldReceive('getAuthenticatedUser')
            ->once()
            ->andReturn(null);

        $result = $this->authService->getAuthenticatedUser();

        $this->assertNull($result);
    }

    public function test_collect_user_details_with_token(): void
    {
        $user = new User(['id' => 1]);
        $userDetails = [
            'id' => 1,
            'full_name' => 'John Doe',
            'email' => 'john@example.com',
            'access_token' => 'test-token',
            'token_type' => 'Bearer',
            'expires_at' => now()->addDays(1),
        ];

        $this->mockAuthRepository
            ->shouldReceive('collectUserDetails')
            ->with($user, true)
            ->once()
            ->andReturn($userDetails);

        $result = $this->authService->collectUserDetails($user, true);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('access_token', $result);
        $this->assertArrayHasKey('token_type', $result);
        $this->assertArrayHasKey('expires_at', $result);
    }

    public function test_collect_user_details_without_token(): void
    {
        $user = new User(['id' => 1]);
        $userDetails = [
            'id' => 1,
            'full_name' => 'John Doe',
            'email' => 'john@example.com',
        ];

        $this->mockAuthRepository
            ->shouldReceive('collectUserDetails')
            ->with($user, false)
            ->once()
            ->andReturn($userDetails);

        $result = $this->authService->collectUserDetails($user, false);

        $this->assertIsArray($result);
        $this->assertArrayNotHasKey('access_token', $result);
        $this->assertArrayNotHasKey('token_type', $result);
        $this->assertArrayNotHasKey('expires_at', $result);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
