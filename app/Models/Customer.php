<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable=[
        'user_id',
    ];
    //Customer may have multiple addresses
    public function address(): HasMany
    {
        return $this->hasMany(Address::class);
    }
    /**
     * Customer has one to one relationship with user
     * @return BelongsToMany
     */
    public function user(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }
}