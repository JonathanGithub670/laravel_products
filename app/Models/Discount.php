<?php

namespace App\Models;

use Database\Factories\DiscountFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $product_id
 * @property float $discount
 * @property Carbon $start_date
 * @property Carbon $end_date
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
#[Fillable(['product_id', 'discount', 'start_date', 'end_date'])]
class Discount extends Model
{
    /** @use HasFactory<DiscountFactory> */
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 't_discounts';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'discount' => 'float',
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    /**
     * Get the product that this discount belongs to.
     *
     * @return BelongsTo<Product, $this>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the records of this discount being applied to sales.
     *
     * @return HasMany<SalesDiscount, $this>
     */
    public function salesDiscounts(): HasMany
    {
        return $this->hasMany(SalesDiscount::class);
    }

    /**
     * Determine whether this discount is valid on the given date (inclusive).
     */
    public function isActiveOn(Carbon $date): bool
    {
        return $date->between($this->start_date, $this->end_date);
    }

    /**
     * Number of times this discount has been applied to sales.
     */
    public function usedCount(): int
    {
        return $this->salesDiscounts()->count();
    }
}
