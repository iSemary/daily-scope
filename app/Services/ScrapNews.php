<?php

namespace App\Services;

use App\Interfaces\ProviderInterface;
use Illuminate\Support\Facades\Log;
use Modules\Provider\Entities\Provider;

class ScrapNews
{
    /**
     * Fetches news from all registered providers.
     * 
     * This method iterates through all providers in the database, checks if they are
     * valid providers according to the ProviderInterface, instantiates their service
     * classes, and calls their fetch() method to retrieve news data. After processing
     * each provider, it updates the fetched_at timestamp to track when the last fetch occurred.
     */
    public function run(): void
    {
        Log::info('Starting news scraping process');

        $providers = Provider::get();
        Log::info('Found ' . $providers->count() . ' providers in database');

        $processedCount = 0;
        $errorCount = 0;

        foreach ($providers as $provider) {
            Log::info("Processing provider: {$provider->name}");

            if (in_array($provider->name, ProviderInterface::PROVIDERS)) {
                $providerClass = $provider->class_name;

                if (class_exists($providerClass)) {
                    try {
                        Log::info("Instantiating provider class: {$providerClass}");
                        $providerService = new $providerClass($provider);

                        if (method_exists($providerService, 'fetch')) {
                            Log::info("Fetching data from provider: {$provider->name}");
                            $providerService->fetch();
                            Log::info("Successfully fetched data from provider: {$provider->name}");
                        } else {
                            Log::warning("Provider class {$providerClass} does not have fetch method");
                        }

                        $provider->update(['fetched_at' => now()]);
                        $processedCount++;
                    } catch (\Exception $e) {
                        Log::error("Error processing provider {$provider->name}: " . $e->getMessage(), [
                            'provider_id' => $provider->id,
                            'provider_name' => $provider->name,
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString()
                        ]);
                        $errorCount++;
                    }
                } else {
                    Log::error("Provider class does not exist: {$providerClass}", [
                        'provider_id' => $provider->id,
                        'provider_name' => $provider->name,
                        'class_name' => $providerClass
                    ]);
                    $errorCount++;
                }
            } else {
                Log::warning("Provider {$provider->name} is not in the allowed providers list", [
                    'provider_id' => $provider->id,
                    'provider_name' => $provider->name,
                    'allowed_providers' => ProviderInterface::PROVIDERS
                ]);
            }
        }

        Log::info("News scraping process completed", [
            'total_providers' => $providers->count(),
            'processed_successfully' => $processedCount,
            'errors' => $errorCount
        ]);
    }
}
