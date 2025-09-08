<?php

namespace modules\Article\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchRequest extends FormRequest {
    public function authorize(): bool {
        return true;
    }

    public function rules(): array {
        return [
            'keyword' => 'required|max:1000|string',
            'date_order' => 'sometimes|in:DESC,ASC',
            'category_id' => 'nullable|numeric',
            'source_id' => 'nullable|numeric',
        ];
    }
}
