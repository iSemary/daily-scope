<?php

namespace Tests\Unit;

use Tests\TestCase;
use Modules\User\Services\UserService;
use Modules\User\Interfaces\UserInterface;
use Modules\User\Entities\User;
use Mockery;

class UserServiceTest extends TestCase
{
    protected UserService $userService;
    protected $mockUserRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mockUserRepository = Mockery::mock(UserInterface::class);
        $this->userService = new UserService($this->mockUserRepository);
    }

    public function test_get_profile(): void
    {
        $user = new User(['id' => 1, 'full_name' => 'John Doe']);
        $profileData = [
            'id' => 1,
            'full_name' => 'John Doe',
            'email' => 'john@example.com',
            'username' => 'johndoe',
        ];

        $this->mockUserRepository
            ->shouldReceive('getUserProfile')
            ->with($user)
            ->once()
            ->andReturn($profileData);

        $result = $this->userService->getProfile($user);

        $this->assertIsArray($result);
        $this->assertEquals($profileData, $result);
    }

    public function test_update_profile_success(): void
    {
        $user = new User(['id' => 1]);
        $validatedData = [
            'full_name' => 'John Updated',
            'email' => 'john.updated@example.com',
        ];

        $this->mockUserRepository
            ->shouldReceive('updateUserProfile')
            ->with($user, $validatedData)
            ->once()
            ->andReturn(true);

        $result = $this->userService->updateProfile($user, $validatedData);

        $this->assertTrue($result);
    }

    public function test_update_profile_failed(): void
    {
        $user = new User(['id' => 1]);
        $validatedData = [
            'full_name' => 'John Updated',
            'email' => 'john.updated@example.com',
        ];

        $this->mockUserRepository
            ->shouldReceive('updateUserProfile')
            ->with($user, $validatedData)
            ->once()
            ->andReturn(false);

        $result = $this->userService->updateProfile($user, $validatedData);

        $this->assertFalse($result);
    }

    public function test_find_by_id(): void
    {
        $user = new User();
        $user->id = 1;
        $user->full_name = 'John Doe';

        $this->mockUserRepository
            ->shouldReceive('findById')
            ->with(1)
            ->once()
            ->andReturn($user);

        $result = $this->userService->findById(1);

        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals(1, $result->id);
    }

    public function test_find_by_id_not_found(): void
    {
        $this->mockUserRepository
            ->shouldReceive('findById')
            ->with(999)
            ->once()
            ->andReturn(null);

        $result = $this->userService->findById(999);

        $this->assertNull($result);
    }

    public function test_find_by_email(): void
    {
        $user = new User(['id' => 1, 'email' => 'john@example.com']);

        $this->mockUserRepository
            ->shouldReceive('findByEmail')
            ->with('john@example.com')
            ->once()
            ->andReturn($user);

        $result = $this->userService->findByEmail('john@example.com');

        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals('john@example.com', $result->email);
    }

    public function test_find_by_email_not_found(): void
    {
        $this->mockUserRepository
            ->shouldReceive('findByEmail')
            ->with('nonexistent@example.com')
            ->once()
            ->andReturn(null);

        $result = $this->userService->findByEmail('nonexistent@example.com');

        $this->assertNull($result);
    }

    public function test_find_by_username(): void
    {
        $user = new User(['id' => 1, 'username' => 'johndoe']);

        $this->mockUserRepository
            ->shouldReceive('findByUsername')
            ->with('johndoe')
            ->once()
            ->andReturn($user);

        $result = $this->userService->findByUsername('johndoe');

        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals('johndoe', $result->username);
    }

    public function test_find_by_username_not_found(): void
    {
        $this->mockUserRepository
            ->shouldReceive('findByUsername')
            ->with('nonexistent')
            ->once()
            ->andReturn(null);

        $result = $this->userService->findByUsername('nonexistent');

        $this->assertNull($result);
    }

    public function test_get_all_users(): void
    {
        $users = [
            ['id' => 1, 'full_name' => 'John Doe'],
            ['id' => 2, 'full_name' => 'Jane Smith'],
        ];

        $this->mockUserRepository
            ->shouldReceive('getAllUsers')
            ->with(10, 0)
            ->once()
            ->andReturn($users);

        $result = $this->userService->getAllUsers(10, 0);

        $this->assertIsArray($result);
        $this->assertEquals($users, $result);
    }

    public function test_get_all_users_with_custom_parameters(): void
    {
        $users = [
            ['id' => 1, 'full_name' => 'John Doe'],
        ];

        $this->mockUserRepository
            ->shouldReceive('getAllUsers')
            ->with(5, 10)
            ->once()
            ->andReturn($users);

        $result = $this->userService->getAllUsers(5, 10);

        $this->assertIsArray($result);
        $this->assertEquals($users, $result);
    }

    public function test_search_users(): void
    {
        $users = [
            ['id' => 1, 'full_name' => 'John Doe'],
            ['id' => 2, 'full_name' => 'John Smith'],
        ];

        $this->mockUserRepository
            ->shouldReceive('searchUsers')
            ->with('john', 10)
            ->once()
            ->andReturn($users);

        $result = $this->userService->searchUsers('john', 10);

        $this->assertIsArray($result);
        $this->assertEquals($users, $result);
    }

    public function test_search_users_with_custom_limit(): void
    {
        $users = [
            ['id' => 1, 'full_name' => 'John Doe'],
        ];

        $this->mockUserRepository
            ->shouldReceive('searchUsers')
            ->with('john', 5)
            ->once()
            ->andReturn($users);

        $result = $this->userService->searchUsers('john', 5);

        $this->assertIsArray($result);
        $this->assertEquals($users, $result);
    }

    public function test_delete_user_success(): void
    {
        $user = new User(['id' => 1]);

        $this->mockUserRepository
            ->shouldReceive('delete')
            ->with($user)
            ->once()
            ->andReturn(true);

        $result = $this->userService->deleteUser($user);

        $this->assertTrue($result);
    }

    public function test_delete_user_failed(): void
    {
        $user = new User(['id' => 1]);

        $this->mockUserRepository
            ->shouldReceive('delete')
            ->with($user)
            ->once()
            ->andReturn(false);

        $result = $this->userService->deleteUser($user);

        $this->assertFalse($result);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
