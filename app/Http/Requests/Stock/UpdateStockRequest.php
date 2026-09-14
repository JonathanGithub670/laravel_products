<?php

namespace App\Http\Requests\Stock;

use App\Models\Stock;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStockRequest extends FormRequest
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
                if ($validator->errors()->hasAny(['product_id', 'qty'])) {
                    return;
                }

                /** @var Stock $stock */
                $stock = $this->route('stock');

                $remaining = $stock->product->stockRemaining() - $stock->qty + $this->integer('qty');

                if ($remaining < 0) {
                    $validator->errors()->add('qty', 'Jumlah pengadaan tidak dapat dikurangi karena stok produk akan menjadi minus.');
                }
            },
        ];
    }
}
