<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends Model
{
    use HasFactory;
    protected $fillable=[
        'user_id',
        'country',
        'county',
        'sub_county',
        'location',
        'sub_location',
        'village',
        'status',//is it active/inactive
    ];
    /**
     * KYC belongs to a particular user
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
