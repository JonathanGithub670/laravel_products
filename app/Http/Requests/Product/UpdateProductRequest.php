<?php

namespace App\Http\Requests\Product;

use App\Models\Product;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'integer', 'min:1'],
            'stock' => ['required', 'integer', 'min:0'],
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @return array<int, callable>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->has('stock')) {
                    return;
                }

                /** @var Product $product */
                $product = $this->route('product');

                // Stock may not fall below the quantity already sold.
                $consumed = $product->stockOut() - $product->stockIn();

                if ($this->integer('stock') < $consumed) {
                    $validator->errors()->add('stock', 'Stok tidak dapat lebih kecil dari jumlah yang sudah terjual.');
                }
            },
        ];
    }
}
