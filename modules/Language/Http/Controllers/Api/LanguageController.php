<?php

namespace modules\Language\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use Illuminate\Http\JsonResponse;
use modules\Language\Entities\Language;

class LanguageController extends ApiController {

    public function index(): JsonResponse {
        $languages = Language::select(['id', 'name', 'code'])->orderBy("name")->get();
        return $this->return(200, "Languages fetched successfully", ['languages' => $languages]);
    }

    public function show(string $code): JsonResponse {
        $language = Language::where('code', $code)->first();
        if (!$language) {
            return $this->return(404, "Language not found");
        }
        return $this->return(200, "Language fetched successfully", ['language' => $language]);
    }
}
