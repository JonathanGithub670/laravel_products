<?php

namespace App\Models;

use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property int $price
 * @property int $stock
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
#[Fillable(['name', 'description', 'price', 'stock'])]
class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'm_products';

    /**
     * Get the stock entries (pengadaan stok) for this product.
     *
     * @return HasMany<Stock, $this>
     */
    public function stocks(): HasMany
    {
        return $this->hasMany(Stock::class);
    }

    /**
     * Get the sales (penjualan) for this product.
     *
     * @return HasMany<Sale, $this>
     */
    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    /**
     * Get the discounts for this product.
     *
     * @return HasMany<Discount, $this>
     */
    public function discounts(): HasMany
    {
        return $this->hasMany(Discount::class);
    }

    /**
     * Total quantity acquired through stock entries.
     */
    public function stockIn(): int
    {
        return (int) $this->stocks()->sum('qty');
    }

    /**
     * Total quantity sold through sales.
     */
    public function stockOut(): int
    {
        return (int) $this->sales()->sum('qty');
    }

    /**
     * Remaining stock: seeded stock + acquisitions - sales (never negative, enforced on write).
     */
    public function stockRemaining(): int
    {
        return max(0, $this->stock + $this->stockIn() - $this->stockOut());
    }

    /**
     * The currently active discount for this product on the given date, if any.
     *
     * The most recently created discount wins when several are active at once.
     */
    public function activeDiscountOn(Carbon $date): ?Discount
    {
        return $this->discounts()
            ->whereDate('start_date', '<=', $date->toDateString())
            ->whereDate('end_date', '>=', $date->toDateString())
            ->orderByDesc('id')
            ->first();
    }
}
