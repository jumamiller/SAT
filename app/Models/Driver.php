<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Driver extends Model
{
    use HasFactory;
    protected $fillable=[
        'user_id',
        'license_number',
        'expiration_date',
        'license_file_path',
        'status'
    ];

    /**
     * Customer to user relationship
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasOne
     */
    public function fleet(): HasOne
    {
        return $this->hasOne(Fleet::class);
    }
}
