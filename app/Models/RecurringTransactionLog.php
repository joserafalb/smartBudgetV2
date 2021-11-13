<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecurringTransactionLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'recurring_transactions_id',
        'creation_date'
    ];

    /**
     * Get the recurring_transactions that owns the RecurringTransactionLog
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function recurring_transactions()
    {
        return $this->belongsTo(RecurringTransactions::class);
    }
}
