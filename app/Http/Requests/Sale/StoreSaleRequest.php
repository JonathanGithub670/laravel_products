<?php

namespace App\Http\Requests\Sale;

use App\Models\Product;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSaleRequest extends FormRequest
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
            'product_id' => ['required', 'integer', Rule::exists('m_products', 'id')->whereNull('deleted_at')],
            'qty' => ['required', 'integer', 'min:1'],
            'sale_date' => ['nullable', 'date'],
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
                if ($validator->errors()->hasAny(['product_id', 'qty', 'sale_date'])) {
                    return;
                }

                $product = Product::find($this->integer('product_id'));

                if ($product === null) {
                    return;
                }

                $remaining = $product->stockRemaining();

                if ($this->integer('qty') > $remaining) {
                    $validator->errors()->add('qty', "Stok tidak mencukupi. Stok produk tersisa: {$remaining}.");
                }
            },
        ];
    }
}
