<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
        $stock = $this->route('stock');
        $stockId = $stock instanceof \App\Models\Stock ? $stock->id : $stock;

        return [
            'product_id' => [
                'required',
                'exists:products,id',
                // Règle corrigée : unique seulement parmi les produits NON supprimés
                Rule::unique('stock_products')->where(function ($query) use ($stockId) {
                    return $query->where('stock_id', $stockId)
                        ->whereNull('deleted_at'); // <--- AJOUTER CECI
                })
            ],
            'provider'         => 'nullable|string|max:255',
            'quantity'         => 'required|integer|min:0',
            'minimum_quantity' => 'required|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.unique' => 'Ce produit existe déjà dans ce stock.',
        ];
    }
}
