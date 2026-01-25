<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StockUserRequest extends FormRequest
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
                'stock_id'   => ['required', 'exists:stocks,id'],
                'user_id'    => ['required', 'exists:users,id'],
                'is_chief'   => ['boolean'],
                'comment'    => ['nullable', 'string'],
                'started_at' => ['nullable', 'date'],
                'ended_at'   => ['nullable', 'date', 'after_or_equal:started_at'],
            ];
        }

        if ($this->isMethod('put') || $this->isMethod('patch')) {
            return [
                'is_chief'   => ['boolean'],
                'comment'    => ['nullable', 'string'],
                'started_at' => ['nullable', 'date'],
                'ended_at'   => ['nullable', 'date', 'after_or_equal:started_at'],
            ];
        }

        return [];
    }
}
