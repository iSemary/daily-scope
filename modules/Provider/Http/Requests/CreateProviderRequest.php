<?php

namespace Modules\Provider\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateProviderRequest extends FormRequest {
    public function authorize(): bool {
        return true;
    }

    public function rules(): array {
        return [
            'name' => 'required|max:255',
            'class_name' => 'required|max:255',
            'end_point' => 'required|max:255',
            'api_key' => 'required|max:255',
        ];
    }
}
