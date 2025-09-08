<?php

namespace modules\Country\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use Illuminate\Http\JsonResponse;
use modules\Country\Entities\Country;

class CountryController extends ApiController {

    public function index(): JsonResponse {
        $countries = Country::select(['id', 'name', 'code'])->orderBy("name")->get();
        return $this->return(200, "Countries fetched successfully", ['countries' => $countries]);
    }

    public function show(string $code): JsonResponse {
        $country = Country::where('code', $code)->first();
        if (!$country) {
            return $this->return(404, "Country not found");
        }
        return $this->return(200, "Country fetched successfully", ['country' => $country]);
    }
}
