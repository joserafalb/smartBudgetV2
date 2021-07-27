<?php

namespace App\Models;

use App\Models\BankAccount;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RecurringTransactions extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'quantity',
        'schedule_type',
        'schedule_parameter',
        'start_date',
        'end_date',
        'category_id',
        'amount',
        'description',
        'bank_account_id',
        'limit_type',
        'active',
    ];

    /**
     * Get the bank account that owns the RecurringTransactions
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class, 'foreign_key', 'other_key');
    }

    /**
     * Get the category that owns the RecurringTransactions
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'foreign_key', 'other_key');
    }
}
