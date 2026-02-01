<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StockMovementRequest extends FormRequest
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
        if ($this->isMethod('post')) {
            return [
                'stock_product_id' => ['required', 'exists:stock_products,id'],
                'movement'         => ['required', 'in:ENTREE,SORTIE'],
                'quantity'         => ['required', 'integer', 'min:1'],
                'price'            => ['nullable', 'numeric', 'min:0'],
                'beneficiary'      => ['nullable', 'string', 'max:255'],
                'proofs'           => ['nullable', 'array'],
                'comment'          => ['nullable', 'string'],
            ];
        }

        if ($this->isMethod('put') || $this->isMethod('patch')) {
            return [
                'quantity'    => ['sometimes', 'integer', 'min:1'],
                'price'       => ['nullable', 'numeric', 'min:0'],
                'beneficiary' => ['nullable', 'string', 'max:255'],
                'proofs'      => ['nullable', 'array'],
                'comment'     => ['nullable', 'string'],
                'validated_by' => ['nullable', 'exists:users,id'],
                'validated_at' => ['nullable', 'date'],
            ];
        }

        return [];
    }
}
