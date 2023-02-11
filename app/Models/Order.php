<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory,SoftDeletes;
    //
    protected $fillable=[
        'customer_id',
        'fleet_id',
        'order_number',
        'total_price',
        'status'
    ];

    /**
     * Order belongs to a customer
     * @return BelongsTo
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Order belongs to a specific fleet
     * @return BelongsTo
     */
    public function fleet(): BelongsTo
    {
        return $this->belongsTo(Fleet::class);
    }

    /**
     * An order may have multiple histories
     * @return HasMany
     */
    public function orderHistories(): HasMany
    {
        return $this->hasMany(OrderHistory::class);
    }
}
