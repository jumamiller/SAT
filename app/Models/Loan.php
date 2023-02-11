<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Loan extends Model
{
    use HasFactory;
    protected $fillable=[
        'account_id',
        'principal',
        'interest_rate',
        'loan_term',
        'repayment_frequency'
    ];

    /**
     * @return BelongsTo
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }
    public function repayment_schedule()
    {
        return $this->hasOne(LoanRepayment::class);
    }
}
