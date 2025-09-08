<?php

namespace modules\Provider\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Crypt;
use modules\Provider\Entities\Provider;
use modules\Provider\Http\Requests\CreateProviderRequest;

class ProviderController extends ApiController {

    public function index(): JsonResponse {
        $providers = Provider::select(['id', 'name', 'class_name', 'end_point', 'fetched_at'])->orderBy("name")->get();
        return $this->return(200, "Providers fetched successfully", ['providers' => $providers]);
    }

    public function show(int $id): JsonResponse {
        $provider = Provider::select(['id', 'name', 'class_name', 'end_point', 'fetched_at'])->find($id);
        if (!$provider) {
            return $this->return(404, "Provider not found");
        }
        return $this->return(200, "Provider fetched successfully", ['provider' => $provider]);
    }

    public function register(CreateProviderRequest $createProviderRequest): JsonResponse {
        $requestData = $createProviderRequest->validated();
        $requestData['api_key'] = Crypt::encrypt($requestData['api_key']);
        Provider::create($requestData);
        return $this->return(200, "Provider Registered Successfully");
    }
}
