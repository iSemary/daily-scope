<?php

namespace Modules\Provider\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Crypt;
use Modules\Provider\Entities\Provider;

class ProviderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $providers = [
            [
                'name' => 'NewsAPI',
                'class_name' => '\App\Services\Providers\NewsAPI',
                'end_point' => 'https://newsapi.org/v2/',
                'env_key' => 'NEWSAPI_API_KEY',
            ],
            [
                'name' => 'NewsDataIO',
                'class_name' => '\App\Services\Providers\NewsDataIO',
                'end_point' => 'https://newsdata.io/api/1/',
                'env_key' => 'NEWSDATAIO_API_KEY',
            ],
            [
                'name' => 'NewsAPIAi',
                'class_name' => '\App\Services\Providers\NewsAPIAi',
                'end_point' => 'http://eventregistry.org/api/v1/',
                'env_key' => 'NEWSAPIAI_API_KEY',
            ],
        ];

        foreach ($providers as $provider) {
            Provider::updateOrCreate(
                ['name' => $provider['name']],
                [
                    'class_name' => $provider['class_name'],
                    'end_point' => $provider['end_point'],
                    'api_key' => Crypt::encrypt(env($provider['env_key'])),
                ]
            );
        }
    }
}
