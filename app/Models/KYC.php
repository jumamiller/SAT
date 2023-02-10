<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KYC extends Model
{
    use HasFactory;
    protected $fillable=[
        'user_id',
        'id_or_passport_front_file_path',//upload front
        'id_or_passport_back_file_path',//Upload back
        'selfie_file_path',//Upload photo
        'status',
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
