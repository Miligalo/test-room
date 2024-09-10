<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $title
 * @property float $price
 * @property int $currency_id
 * @property string $created_at
 * @property string $updated_at
 */
class Product extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }
}
