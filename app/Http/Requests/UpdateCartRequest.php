<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCartRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'variant_id' => [
                'required',
                'integer',
                'exists:product_variants,id',
            ],

            'quantity' => [
                'required',
                'integer',
                'min:1',
                'max:10',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'variant_id.required' =>
                'Please select a size.',

            'variant_id.exists' =>
                'The selected size is unavailable.',

            'quantity.required' =>
                'Please enter a quantity.',

            'quantity.min' =>
                'The quantity must be at least 1.',

            'quantity.max' =>
                'The maximum quantity is 10.',
        ];
    }
}