<?php

namespace Modules\Country\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use Illuminate\Http\JsonResponse;
use Modules\Country\Services\CountryService;

class CountryController extends ApiController
{
    private CountryService $countryService;

    public function __construct(CountryService $countryService)
    {
        $this->countryService = $countryService;
    }

    public function index(): JsonResponse
    {
        $countries = $this->countryService->list();
        return $this->return(200, "Countries fetched successfully", ['countries' => $countries]);
    }

    public function show(string $code): JsonResponse
    {
        $country = $this->countryService->showByCode($code);
        if (!$country) {
            return $this->return(404, "Country not found");
        }
        return $this->return(200, "Country fetched successfully", ['country' => $country]);
    }
}
