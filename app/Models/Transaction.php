<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;
    //
    protected $fillable=[
        'account_id',
        'transaction_code',
        'amount',
        'sender_account_number',
        'receiver_account_number',
        'transaction_reference',
        'status'
    ];

}
