<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => [
                'required',
                Rule::exists('categories', 'id')
                    ->where('is_active', true),
            ],

            'name' => [
                'required',
                'string',
                'max:150',
                'unique:products,name',
            ],

            'description' => [
                'required',
                'string',
                'max:3000',
            ],

            'price' => [
                'required',
                'numeric',
                'min:0.01',
                'max:999999.99',
            ],

            'audience' => [
                'required',
                Rule::in(['men', 'women', 'kids']),
            ],

            'image' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:3072',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],

            'is_featured' => [
                'nullable',
                'boolean',
            ],

            'variants' => [
                'required',
                'array',
                'min:1',
            ],

            'variants.*.size' => [
                'required',
                'string',
                'max:20',
            ],

            'variants.*.color' => [
                'required',
                'string',
                'max:50',
            ],

            'variants.*.stock' => [
                'required',
                'integer',
                'min:0',
                'max:100000',
            ],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $variants = collect($this->input('variants', []));

                $combinations = $variants->map(function (array $variant) {
                    $size = strtolower(trim($variant['size'] ?? ''));
                    $color = strtolower(trim($variant['color'] ?? ''));

                    return "{$size}|{$color}";
                });

                if ($combinations->count() !== $combinations->unique()->count()) {
                    $validator->errors()->add(
                        'variants',
                        'The same size and color combination cannot be repeated.'
                    );
                }
            },
        ];
    }
}