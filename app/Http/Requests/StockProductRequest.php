<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StockProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $isPost = $this->isMethod('post');

        return [
            'stock_id'         => $isPost ? 'required|exists:stocks,id' : 'sometimes|exists:stocks,id',
            'product_id'       => $isPost ? 'required|exists:products,id' : 'sometimes|exists:products,id',
            'provider'         => 'nullable|string|max:255',
            'quantity'         => 'required|integer|min:0',
            'minimum_quantity' => 'required|integer|min:0',
        ];
    }
}
