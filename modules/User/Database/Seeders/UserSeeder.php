<?php

namespace Modules\User\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Modules\Country\Entities\Country;
use Modules\User\Entities\User;

class UserSeeder extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run(): void {
        // Test User
        User::updateOrCreate(
            ['email' => 'user@test.com'],
            [
                'full_name' => 'Test User',
                'phone' => '1527015337',
                'username' => 'testuser' . Str::random(4),
                'country_id' => Country::getCountryIdByCode('DE'),
                'password' => '123456789',
            ]
        );
    }
}
