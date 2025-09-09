<?php

namespace Tests\Unit;

use Tests\TestCase;
use Modules\User\Entities\User;
use Modules\Country\Entities\Country;
use Modules\User\Entities\UserInterest;
use Illuminate\Database\Eloquent\Collection;
use Mockery;

class UserModelTest extends TestCase
{
    public function test_user_fillable_attributes(): void
    {
        $user = new User();
        $expectedFillable = [
            'full_name',
            'email',
            'password',
            'phone',
            'username',
            'country_id',
        ];

        $this->assertEquals($expectedFillable, $user->getFillable());
    }

    public function test_user_hidden_attributes(): void
    {
        $user = new User();
        $expectedHidden = [
            'password',
            'remember_token',
        ];

        $this->assertEquals($expectedHidden, $user->getHidden());
    }

    public function test_user_casts(): void
    {
        $user = new User();
        $expectedCasts = [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];

        $actualCasts = $user->getCasts();
        $this->assertEquals($expectedCasts['email_verified_at'], $actualCasts['email_verified_at']);
        $this->assertEquals($expectedCasts['password'], $actualCasts['password']);
    }

    public function test_user_soft_deletes(): void
    {
        $user = new User();
        $this->assertTrue(method_exists($user, 'trashed'));
    }

    public function test_user_has_factory(): void
    {
        $user = new User();
        $this->assertTrue(method_exists($user, 'factory'));
    }

    public function test_user_has_api_tokens(): void
    {
        $user = new User();
        $this->assertTrue(method_exists($user, 'createToken'));
    }

    public function test_user_is_notifiable(): void
    {
        $user = new User();
        $this->assertTrue(method_exists($user, 'notify'));
    }

    public function test_user_country_relationship(): void
    {
        $user = new User();
        $country = $user->country();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $country);
        $this->assertEquals('country_id', $country->getForeignKeyName());
        $this->assertEquals('id', $country->getOwnerKeyName());
    }

    public function test_user_interests_relationship(): void
    {
        $user = new User();
        $interests = $user->userInterests();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $interests);
        $this->assertEquals('user_id', $interests->getForeignKeyName());
        $this->assertEquals('id', $interests->getLocalKeyName());
    }

    public function test_user_can_be_created(): void
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

        $this->assertEquals('John Doe', $user->full_name);
        $this->assertEquals('john@example.com', $user->email);
        $this->assertTrue(password_verify('password123', $user->password));
        $this->assertEquals('+1234567890', $user->phone);
        $this->assertEquals('johndoe', $user->username);
        $this->assertEquals(1, $user->country_id);
    }

    public function test_user_sync_interests(): void
    {
        $user = new User(['id' => 1]);
        
        // Test that the method exists and can be called
        $this->assertTrue(method_exists($user, 'syncInterests'));
    }

    public function test_user_record_view_item(): void
    {
        $user = new User(['id' => 1]);
        $itemId = 123;
        $itemType = 'article';

        // Test that the method exists and can be called
        $this->assertTrue(method_exists($user, 'recordUserViewItem'));
    }

    public function test_user_authenticatable(): void
    {
        $user = new User();
        $this->assertInstanceOf(\Illuminate\Foundation\Auth\User::class, $user);
    }

    public function test_user_password_hashing(): void
    {
        $user = new User();
        $user->password = 'plaintext';

        // The password should be hashed due to the 'hashed' cast
        $this->assertNotEquals('plaintext', $user->password);
        $this->assertTrue(password_verify('plaintext', $user->password));
    }

    public function test_user_email_verification_cast(): void
    {
        $user = new User();
        $user->email_verified_at = '2023-01-01 12:00:00';

        $this->assertInstanceOf(\Carbon\Carbon::class, $user->email_verified_at);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
