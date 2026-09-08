<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PlaceOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'customer_name' => [
                'required',
                'string',
                'min:3',
                'max:100',
            ],

            'phone' => [
                'required',
                'string',
                'max:20',
                'regex:/^(?:\+962|00962|0)?7[789]\d{7}$/',
            ],

            'city' => [
                'required',
                Rule::in([
                    'Amman',
                    'Zarqa',
                    'Irbid',
                    'Aqaba',
                    'Salt',
                    'Madaba',
                    'Jerash',
                    'Ajloun',
                    'Mafraq',
                    'Karak',
                    'Tafilah',
                    'Maan',
                ]),
            ],

            'address' => [
                'required',
                'string',
                'min:10',
                'max:500',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:500',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.regex' =>
                'Please enter a valid Jordanian mobile number.',
        ];
    }
}