<?php

namespace App\Models;

use Database\Factories\SaleFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $product_id
 * @property int $qty
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
#[Fillable(['product_id', 'qty'])]
class Sale extends Model
{
    /** @use HasFactory<SaleFactory> */
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 't_sales';

    /**
     * Get the product that this sale belongs to.
     *
     * @return BelongsTo<Product, $this>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the discounts applied to this sale.
     *
     * @return HasMany<SalesDiscount, $this>
     */
    public function salesDiscounts(): HasMany
    {
        return $this->hasMany(SalesDiscount::class, 'sales_id');
    }

    /**
     * Get the most recently applied discount for this sale, if any.
     *
     * @return HasOne<SalesDiscount, $this>
     */
    public function latestSalesDiscount(): HasOne
    {
        return $this->hasOne(SalesDiscount::class, 'sales_id')->latestOfMany();
    }

    /**
     * Gross total of this sale before any discount.
     */
    public function grossTotal(): float
    {
        return (float) ($this->qty * $this->product->price);
    }

    /**
     * Total of this sale after the applied discount (discount is a percent of 100).
     */
    public function discountedTotal(): float
    {
        $salesDiscount = $this->latestSalesDiscount;

        return $salesDiscount === null
            ? $this->grossTotal()
            : $this->grossTotal() * (1 - ($salesDiscount->discount->discount / 100));
    }
}
