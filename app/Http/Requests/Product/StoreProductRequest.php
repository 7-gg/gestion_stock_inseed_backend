<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() != null; // allow logged users to create product, policy could be added
    }

    public function rules(): array
    {
        return [
            'name'            => ['required', 'string', 'max:255'],
            'category_id'     => ['required', 'exists:product_categories,id'],
            'unit_id'         => ['required', 'exists:product_units,id'],
            'characteristics' => ['nullable', 'array'],
            'history'         => ['nullable', 'array'],
        ];
    }
}
