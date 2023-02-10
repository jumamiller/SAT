<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Account extends Model
{
    use HasFactory;
    protected $fillable=[
        "user_id",
        "account_name",
        "account_number",
        "account_balance",
        "account_limit",
        "status",
    ];
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
