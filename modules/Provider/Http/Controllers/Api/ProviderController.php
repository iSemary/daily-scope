<?php

namespace modules\Provider\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Services\ScrapNews;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Crypt;
use modules\Provider\Services\ProviderService;
use modules\Provider\Http\Requests\CreateProviderRequest;

class ProviderController extends ApiController
{
    private ProviderService $providerService;

    public function __construct(ProviderService $providerService)
    {
        $this->providerService = $providerService;
    }

    public function index(): JsonResponse
    {
        $providers = $this->providerService->list();
        return $this->return(200, "Providers fetched successfully", ['providers' => $providers]);
    }

    public function show(int $id): JsonResponse
    {
        $provider = $this->providerService->show($id);
        if (!$provider) {
            return $this->return(404, "Provider not found");
        }
        return $this->return(200, "Provider fetched successfully", ['provider' => $provider]);
    }

    public function register(CreateProviderRequest $createProviderRequest): JsonResponse
    {
        $requestData = $createProviderRequest->validated();
        $requestData['api_key'] = Crypt::encrypt($requestData['api_key']);
        $this->providerService->register($requestData);
        return $this->return(200, "Provider Registered Successfully");
    }

    public function sync(): JsonResponse
    {
        $this->providerService->sync();
        return $this->return(200, "News sync completed successfully");
    }
}
